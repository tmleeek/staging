<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Synchronization_ListingsProducts_Update
    extends Ess_M2ePro_Model_Ebay_Synchronization_ListingsProducts_Abstract
{
    const EBAY_STATUS_ACTIVE = 'Active';
    const EBAY_STATUS_ENDED = 'Ended';
    const EBAY_STATUS_COMPLETED = 'Completed';

    private $logsActionId = NULL;

    private $listingsProductsLockStatus = array();

    private $listingsProductsIdsForNeedSynchRulesCheck = array();

    //########################################

    /**
     * @return string
     */
    protected function getNick()
    {
        return '/update/';
    }

    /**
     * @return string
     */
    protected function getTitle()
    {
        return 'Update Listings Products';
    }

    // ---------------------------------------

    /**
     * @return int
     */
    protected function getPercentsStart()
    {
        return 30;
    }

    /**
     * @return int
     */
    protected function getPercentsEnd()
    {
        return 100;
    }

    //########################################

    protected function performActions()
    {
        $accounts = Mage::helper('M2ePro/Component_Ebay')->getCollection('Account')->getItems();

        if (count($accounts) <= 0) {
            return;
        }

        $iteration = 0;
        $percentsForOneStep = $this->getPercentsInterval() / count($accounts);

        foreach ($accounts as $account) {

            /** @var $account Ess_M2ePro_Model_Account **/

            $this->getActualOperationHistory()->addText('Starting Account "'.$account->getTitle().'"');
            // M2ePro_TRANSLATIONS
            // The "Update Listings Products" Action for eBay Account: "%account_title%" is started. Please wait...
            $status = 'The "Update Listings Products" Action for eBay Account: "%account_title%" is started. ';
            $status .= 'Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));

            $this->getActualOperationHistory()->addTimePoint(
                __METHOD__.'process'.$account->getId(),
                'Process Account '.$account->getTitle()
            );

            try {

                $this->processAccount($account);

            } catch (Exception $exception) {

                $message = Mage::helper('M2ePro')->__(
                    'The "Update Listings Products" Action for eBay Account: "%account%" was completed with error.',
                    $account->getTitle()
                );

                $this->processTaskAccountException($message, __FILE__, __LINE__);
                $this->processTaskException($exception);
            }

            $this->getActualOperationHistory()->saveTimePoint(__METHOD__.'process'.$account->getId());

            // M2ePro_TRANSLATIONS
            // The "Update Listings Products" Action for eBay Account: "%account_title%" is finished. Please wait...
            $status = 'The "Update Listings Products" Action for eBay Account: "%account_title%" is finished.'.
                ' Please wait...';
            $this->getActualLockItem()->setStatus(Mage::helper('M2ePro')->__($status, $account->getTitle()));
            $this->getActualLockItem()->setPercents($this->getPercentsStart() + $iteration * $percentsForOneStep);
            $this->getActualLockItem()->activate();

            $iteration++;
        }

        if (!empty($this->listingsProductsIdsForNeedSynchRulesCheck)) {
            Mage::getResourceModel('M2ePro/Listing_Product')->setNeedSynchRulesCheck(
                array_unique($this->listingsProductsIdsForNeedSynchRulesCheck)
            );
        }
    }

    // ---------------------------------------

    private function processAccount(Ess_M2ePro_Model_Account $account)
    {
        $sinceTime = $this->prepareSinceTime($account->getData('defaults_last_synchronization'));
        $changesByAccount = $this->getChangesByAccount($account, $sinceTime);

        if (!isset($changesByAccount['items']) || !isset($changesByAccount['to_time'])) {
            return;
        }

        $account->getChildObject()->setData('defaults_last_synchronization', $changesByAccount['to_time'])->save();

        Mage::helper('M2ePro/Data_Cache_Session')->setValue(
            'item_get_changes_data_' . $account->getId(), $changesByAccount
        );

        foreach ($changesByAccount['items'] as $change) {

            /* @var $listingProduct Ess_M2ePro_Model_Listing_Product */

            $listingProduct = Mage::helper('M2ePro/Component_Ebay')->getListingProductByEbayItem(
                $change['id'], $account->getId()
            );

            if (is_null($listingProduct)) {
                continue;
            }

            /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
            $ebayListingProduct = $listingProduct->getChildObject();

            $isVariationOnChannel = !empty($change['variations']);
            $isVariationInMagento = $ebayListingProduct->isVariationsReady();

            if ($isVariationOnChannel != $isVariationInMagento) {
                continue;
            }

            // Listing product isn't listed and it child must have another item_id
            if ($listingProduct->getStatus() != Ess_M2ePro_Model_Listing_Product::STATUS_LISTED &&
                $listingProduct->getStatus() != Ess_M2ePro_Model_Listing_Product::STATUS_HIDDEN) {
                continue;
            }

            $this->listingsProductsLockStatus[$listingProduct->getId()] =
                $listingProduct->isSetProcessingLock('in_action');

            $dataForUpdate = array_merge(
                $this->getProductDatesChanges($listingProduct, $change),
                $this->getProductStatusChanges($listingProduct, $change),
                $this->getProductQtyChanges($listingProduct, $change)
            );

            if (!$isVariationOnChannel || !$isVariationInMagento) {
                $dataForUpdate = array_merge(
                    $dataForUpdate,
                    $this->getSimpleProductPriceChanges($listingProduct, $change)
                );

                $listingProduct->addData($dataForUpdate)->save();
            } else {

                $listingProductVariations = $listingProduct->getVariations(true);

                $this->processVariationChanges($listingProduct, $listingProductVariations, $change['variations']);

                $dataForUpdate = array_merge(
                    $dataForUpdate,
                    $this->getVariationProductPriceChanges($listingProduct, $listingProductVariations)
                );

                $oldListingProductStatus = $listingProduct->getStatus();

                $listingProduct->addData($dataForUpdate)->save();

                if ($oldListingProductStatus != $listingProduct->getStatus()) {
                    $ebayListingProduct->updateVariationsStatus();
                }
            }
        }
    }

    //########################################

    private function getChangesByAccount(Ess_M2ePro_Model_Account $account, $sinceTime)
    {
        $nextSinceTime = new DateTime($sinceTime, new DateTimeZone('UTC'));

        $toTime = NULL;

        $operationHistory = $this->getActualOperationHistory()->getParentObject('synchronization');
        if (!is_null($operationHistory)) {
            $toTime = $operationHistory->getData('start_date');

            if ($nextSinceTime->format('U') >= strtotime($toTime)) {
                $nextSinceTime = new DateTime($toTime, new DateTimeZone('UTC'));
                $nextSinceTime->modify('- 1 minute');
            }
        }

        $response = $this->receiveChangesFromEbay(
            $account, array('since_time' => $nextSinceTime->format('Y-m-d H:i:s'), 'to_time' => $toTime)
        );

        if ($response) {
            return (array)$response;
        }

        $previousSinceTime = $nextSinceTime;

        $nextSinceTime = new DateTime('now', new DateTimeZone('UTC'));
        $nextSinceTime->modify("-1 day");

        if ($previousSinceTime->format('U') < $nextSinceTime->format('U')) {

            // from day behind now
            $response = $this->receiveChangesFromEbay(
                $account, array('since_time' => $nextSinceTime->format('Y-m-d H:i:s'), 'to_time' => $toTime)
            );

            if ($response) {
                return (array)$response;
            }

            $previousSinceTime = $nextSinceTime;
        }

        $nextSinceTime = new DateTime('now', new DateTimeZone('UTC'));

        if ($previousSinceTime->format('U') < $nextSinceTime->format('U')) {

            // from now
            $response = $this->receiveChangesFromEbay(
                $account, array('since_time' => $nextSinceTime->format('Y-m-d H:i:s'), 'to_time' => $toTime)
            );

            if ($response) {
                return (array)$response;
            }
        }

        return array();
    }

    private function receiveChangesFromEbay(Ess_M2ePro_Model_Account $account, array $paramsConnector = array())
    {
        $dispatcherObj = Mage::getModel('M2ePro/Ebay_Connector_Dispatcher');
        $connectorObj = $dispatcherObj->getVirtualConnector(
            'item', 'get', 'changes',
            $paramsConnector, NULL,
            NULL, $account->getId()
        );

        $dispatcherObj->process($connectorObj);
        $this->processResponseMessages($connectorObj->getResponseMessages());

        $responseData = $connectorObj->getResponseData();

        if (!isset($responseData['items']) || !isset($responseData['to_time'])) {
            return NULL;
        }

        return $responseData;
    }

    private function processResponseMessages(array $messages)
    {
        /** @var Ess_M2ePro_Model_Connector_Connection_Response_Message_Set $messagesSet */
        $messagesSet = Mage::getModel('M2ePro/Connector_Connection_Response_Message_Set');
        $messagesSet->init($messages);

        foreach ($messagesSet->getEntities() as $message) {

            if ($message->getCode() == 21917062) {
                continue;
            }

            if (!$message->isError() && !$message->isWarning()) {
                continue;
            }

            $logType = $message->isError() ? Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR
                : Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING;

            $this->getLog()->addMessage(
                Mage::helper('M2ePro')->__($message->getText()),
                $logType,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
            );
        }
    }

    //########################################

    private function getProductDatesChanges(Ess_M2ePro_Model_Listing_Product $listingProduct, array $change)
    {
        return array(
            'start_date' => Ess_M2ePro_Model_Ebay_Connector_Command_RealTime::ebayTimeToString($change['startTime']),
            'end_date' => Ess_M2ePro_Model_Ebay_Connector_Command_RealTime::ebayTimeToString($change['endTime'])
        );
    }

    private function getProductStatusChanges(Ess_M2ePro_Model_Listing_Product $listingProduct, array $change)
    {
        $data = array();

        $qty = (int)$change['quantity'] < 0 ? 0 : (int)$change['quantity'];
        $qtySold = (int)$change['quantitySold'] < 0 ? 0 : (int)$change['quantitySold'];

        if (($change['listingStatus'] == self::EBAY_STATUS_COMPLETED ||
             $change['listingStatus'] == self::EBAY_STATUS_ENDED) &&
             $listingProduct->getStatus() != Ess_M2ePro_Model_Listing_Product::STATUS_HIDDEN &&
             $qty == $qtySold) {

            $data['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_SOLD;

        } else if ($change['listingStatus'] == self::EBAY_STATUS_COMPLETED) {

            $data['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED;

        } else if ($change['listingStatus'] == self::EBAY_STATUS_ENDED) {

            $data['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_FINISHED;

        } else if ($change['listingStatus'] == self::EBAY_STATUS_ACTIVE &&
                   $qty - $qtySold <= 0) {

            $data['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_HIDDEN;

        } else if ($change['listingStatus'] == self::EBAY_STATUS_ACTIVE) {

            $data['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_LISTED;
        }

        $accountOutOfStockControl = $listingProduct->getAccount()->getChildObject()->getOutOfStockControl(true);

        if (isset($change['out_of_stock'])) {

            $data['additional_data'] = array('out_of_stock_control' => (bool)$change['out_of_stock']);

        } elseif ($data['status'] == Ess_M2ePro_Model_Listing_Product::STATUS_HIDDEN &&
            !is_null($accountOutOfStockControl) && !$accountOutOfStockControl) {

            // Listed Hidden Status can be only for GTC items
            if (is_null($listingProduct->getChildObject()->getOnlineDuration())) {
                $data['online_duration'] = Ess_M2ePro_Helper_Component_Ebay::LISTING_DURATION_GTC;
            }

            $additionalData = $listingProduct->getAdditionalData();
            empty($additionalData['out_of_stock_control']) && $additionalData['out_of_stock_control'] = true;
            $data['additional_data'] = Mage::helper('M2ePro')->jsonEncode($additionalData);
        }

        if ($listingProduct->getStatus() == $data['status']) {
            return $data;
        }

        $data['status_changer'] = Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_COMPONENT;

        $statusChangedFrom = Mage::helper('M2ePro/Component_Ebay')
            ->getHumanTitleByListingProductStatus($listingProduct->getStatus());
        $statusChangedTo = Mage::helper('M2ePro/Component_Ebay')
            ->getHumanTitleByListingProductStatus($data['status']);

        if (!empty($statusChangedFrom) && !empty($statusChangedTo)) {
            // M2ePro_TRANSLATIONS
            // Item Status was successfully changed from "%from%" to "%to%" .
            $this->logReportChange($listingProduct, Mage::helper('M2ePro')->__(
                'Item Status was successfully changed from "%from%" to "%to%" .',
                $statusChangedFrom,
                $statusChangedTo
            ));
        }

        Mage::getModel('M2ePro/ProductChange')->addUpdateAction(
            $listingProduct->getProductId(), Ess_M2ePro_Model_ProductChange::INITIATOR_SYNCHRONIZATION
        );

        if ($this->listingsProductsLockStatus[$listingProduct->getId()]) {
            $this->listingsProductsIdsForNeedSynchRulesCheck[] = $listingProduct->getId();
        }

        return $data;
    }

    private function getProductQtyChanges(Ess_M2ePro_Model_Listing_Product $listingProduct, array $change)
    {
        $data = array();

        /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
        $ebayListingProduct = $listingProduct->getChildObject();

        $data['online_qty'] = (int)$change['quantity'] < 0 ? 0 : (int)$change['quantity'];
        $data['online_qty_sold'] = (int)$change['quantitySold'] < 0 ? 0 : (int)$change['quantitySold'];

        if ($ebayListingProduct->isVariationsReady()) {
            return $data;
        }

        $listingType = $this->getActualListingType($listingProduct, $change);

        if ($listingType == Ess_M2ePro_Model_Ebay_Template_SellingFormat::LISTING_TYPE_AUCTION) {
            $data['online_qty'] = 1;
            $data['online_bids'] = (int)$change['bidCount'] < 0 ? 0 : (int)$change['bidCount'];
        }

        if ($ebayListingProduct->getOnlineQty() != $data['online_qty'] ||
            $ebayListingProduct->getOnlineQtySold() != $data['online_qty_sold']) {
            // M2ePro_TRANSLATIONS
            // Item QTY was successfully changed from %from% to %to% .
            $this->logReportChange($listingProduct, Mage::helper('M2ePro')->__(
                'Item QTY was successfully changed from %from% to %to% .',
                ($ebayListingProduct->getOnlineQty() - $ebayListingProduct->getOnlineQtySold()),
                ($data['online_qty'] - $data['online_qty_sold'])
            ));

            Mage::getModel('M2ePro/ProductChange')->addUpdateAction(
                $listingProduct->getProductId(), Ess_M2ePro_Model_ProductChange::INITIATOR_SYNCHRONIZATION
            );

            if ($this->listingsProductsLockStatus[$listingProduct->getId()]) {
                $this->listingsProductsIdsForNeedSynchRulesCheck[] = $listingProduct->getId();
            }
        }

        return $data;
    }

    // ---------------------------------------

    private function getSimpleProductPriceChanges(Ess_M2ePro_Model_Listing_Product $listingProduct, array $change)
    {
        $data = array();

        /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
        $ebayListingProduct = $listingProduct->getChildObject();

        if ($ebayListingProduct->isVariationsReady()) {
            return $data;
        }

        $data['online_current_price'] = (float)$change['currentPrice'] < 0 ? 0 : (float)$change['currentPrice'];
        /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
        $ebayListingProduct = $listingProduct->getChildObject();

        $listingType = $this->getActualListingType($listingProduct, $change);

        if ($listingType == Ess_M2ePro_Model_Ebay_Template_SellingFormat::LISTING_TYPE_FIXED) {

            if ($ebayListingProduct->getOnlineCurrentPrice() != $data['online_current_price']) {
                // M2ePro_TRANSLATIONS
                // Item Price was successfully changed from %from% to %to% .
                $this->logReportChange($listingProduct, Mage::helper('M2ePro')->__(
                    'Item Price was successfully changed from %from% to %to% .',
                    $ebayListingProduct->getOnlineCurrentPrice(),
                    $data['online_current_price']
                ));

                Mage::getModel('M2ePro/ProductChange')->addUpdateAction(
                    $listingProduct->getProductId(), Ess_M2ePro_Model_ProductChange::INITIATOR_SYNCHRONIZATION
                );
            }
        }

        return $data;
    }

    // ---------------------------------------

    /**
     * @param Ess_M2ePro_Model_Listing_Product $listingProduct
     * @param Ess_M2ePro_Model_Listing_Product_Variation[] $variations
     * @return array
     */
    private function getVariationProductPriceChanges(Ess_M2ePro_Model_Listing_Product $listingProduct,
                                                     array $variations)
    {
        /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
        $ebayListingProduct = $listingProduct->getChildObject();

        $calculateWithEmptyQty = $ebayListingProduct->isOutOfStockControlEnabled();

        $onlineCurrentPrice  = NULL;

        foreach ($variations as $variation) {

            /** @var Ess_M2ePro_Model_Ebay_Listing_Product_Variation $ebayVariation */
            $ebayVariation = $variation->getChildObject();

            if (!$calculateWithEmptyQty && $ebayVariation->getOnlineQty() <= 0) {
                continue;
            }

            if (!is_null($onlineCurrentPrice) && $ebayVariation->getOnlinePrice() >= $onlineCurrentPrice) {
                continue;
            }

            $onlineCurrentPrice = $ebayVariation->getOnlinePrice();
        }

        return array('online_current_price' => $onlineCurrentPrice);
    }

    //########################################

    private function processVariationChanges(Ess_M2ePro_Model_Listing_Product $listingProduct,
                                             array $listingProductVariations, array $changeVariations)
    {
        $variationsSnapshot = $this->getVariationsSnapshot($listingProductVariations);
        if (count($variationsSnapshot) <= 0) {
            return;
        }

        $hasVariationPriceChanges = false;
        $hasVariationQtyChanges   = false;

        foreach ($changeVariations as $changeVariation) {
            foreach ($variationsSnapshot as $variationSnapshot) {

                if (!$this->isVariationEqualWithChange($listingProduct,$changeVariation,$variationSnapshot)) {
                    continue;
                }

                $updateData = array(
                    'online_price' => (float)$changeVariation['price'] < 0 ? 0 : (float)$changeVariation['price'],
                    'online_qty' => (int)$changeVariation['quantity'] < 0 ? 0 : (int)$changeVariation['quantity'],
                    'online_qty_sold' => (int)$changeVariation['quantitySold'] < 0 ?
                        0 : (int)$changeVariation['quantitySold']
                );

                /** @var Ess_M2ePro_Model_Ebay_Listing_Product_Variation $ebayVariation */
                $ebayVariation = $variationSnapshot['variation']->getChildObject();

                if ($this->listingsProductsLockStatus[$listingProduct->getId()] &&
                    ($ebayVariation->getOnlineQty() != $updateData['online_qty'] ||
                     $ebayVariation->getOnlineQtySold() != $updateData['online_qty_sold'])
                ) {
                    $this->listingsProductsIdsForNeedSynchRulesCheck[] = $listingProduct->getId();
                }

                $isVariationChanged = false;

                if ($ebayVariation->getOnlinePrice() != $updateData['online_price']) {
                    $hasVariationPriceChanges = true;
                    $isVariationChanged       = true;
                }

                if ($ebayVariation->getOnlineQty() != $updateData['online_qty'] ||
                    $ebayVariation->getOnlineQtySold() != $updateData['online_qty_sold']) {

                    $hasVariationQtyChanges = true;
                    $isVariationChanged     = true;
                }

                if ($isVariationChanged) {
                    $variationSnapshot['variation']->addData($updateData)->save();
                    $variationSnapshot['variation']->getChildObject()->setStatus($listingProduct->getStatus());
                }

                break;
            }
        }

        if ($hasVariationPriceChanges) {
            $this->logReportChange($listingProduct, Mage::helper('M2ePro')->__(
                'Price of some Variations was successfully changed.'
            ));
        }

        if ($hasVariationQtyChanges) {
            $this->logReportChange($listingProduct, Mage::helper('M2ePro')->__(
                'QTY of some Variations was successfully changed.'
            ));
        }

        if ($hasVariationPriceChanges || $hasVariationQtyChanges) {
            Mage::getModel('M2ePro/ProductChange')->addUpdateAction(
                $listingProduct->getProductId(), Ess_M2ePro_Model_ProductChange::INITIATOR_SYNCHRONIZATION
            );
        }
    }

    //########################################

    /**
     * @param Ess_M2ePro_Model_Listing_Product_Variation[] $variations
     * @return array
     */
    private function getVariationsSnapshot(array $variations)
    {
        $variationIds = array();
        foreach ($variations as $variation) {
            $variationIds[] = $variation->getId();
        }

        /** @var Ess_M2ePro_Model_Mysql4_Listing_Product_Variation_Option_Collection $optionCollection */
        $optionCollection = Mage::helper('M2ePro/Component_Ebay')->getCollection('Listing_Product_Variation_Option');
        $optionCollection->addFieldToFilter('listing_product_variation_id', array('in' => $variationIds));

        $snapshot = array();

        foreach ($variations as $variation) {

            $options = $optionCollection->getItemsByColumnValue('listing_product_variation_id', $variation->getId());

            if (count($options) <= 0) {
                continue;
            }

            $snapshot[] = array(
                'variation' => $variation,
                'options'   => $options
            );
        }

        return $snapshot;
    }

    private function isVariationEqualWithChange(Ess_M2ePro_Model_Listing_Product $listingProduct,
                                                array $changeVariation, array $variationSnapshot)
    {
        if (count($variationSnapshot['options']) != count($changeVariation['specifics'])) {
            return false;
        }

        $specificsReplacements = $listingProduct->getSetting(
            'additional_data', 'variations_specifics_replacements', array()
        );

        foreach ($variationSnapshot['options'] as $variationSnapshotOption) {
            /** @var Ess_M2ePro_Model_Listing_Product_Variation_Option $variationSnapshotOption */

            $variationSnapshotOptionName  = $variationSnapshotOption->getData('attribute');
            $variationSnapshotOptionValue = $variationSnapshotOption->getData('option');

            if (array_key_exists($variationSnapshotOptionName, $specificsReplacements)) {
                $variationSnapshotOptionName = $specificsReplacements[$variationSnapshotOptionName];
            }

            $haveOption = false;

            foreach ($changeVariation['specifics'] as $changeVariationOption=>$changeVariationValue) {

                if ($variationSnapshotOptionName == $changeVariationOption &&
                    $variationSnapshotOptionValue == $changeVariationValue)
                {
                    $haveOption = true;
                    break;
                }
            }

            if ($haveOption === false) {
                return false;
            }
        }

        return true;
    }

    //########################################

    private function prepareSinceTime($sinceTime)
    {
        $minTime = new DateTime('now', new DateTimeZone('UTC'));
        $minTime->modify("-1 month");

        if (empty($sinceTime) || strtotime($sinceTime) < (int)$minTime->format('U')) {
            $sinceTime = new DateTime('now', new DateTimeZone('UTC'));
            $sinceTime = $sinceTime->format('Y-m-d H:i:s');
        }

        return $sinceTime;
    }

    // ---------------------------------------

    private function getLogsActionId()
    {
        if (is_null($this->logsActionId)) {
            $this->logsActionId = Mage::getModel('M2ePro/Listing_Log')->getNextActionId();
        }
        return $this->logsActionId;
    }

    private function getActualListingType(Ess_M2ePro_Model_Listing_Product $listingProduct, array $change)
    {
        $validEbayValues = array(
            Ess_M2ePro_Model_Ebay_Listing_Product_Action_Request_Selling::LISTING_TYPE_AUCTION,
            Ess_M2ePro_Model_Ebay_Listing_Product_Action_Request_Selling::LISTING_TYPE_FIXED
        );

        if (isset($change['listingType']) && in_array($change['listingType'],$validEbayValues)) {

            switch ($change['listingType']) {
                case Ess_M2ePro_Model_Ebay_Listing_Product_Action_Request_Selling::LISTING_TYPE_AUCTION:
                    $result = Ess_M2ePro_Model_Ebay_Template_SellingFormat::LISTING_TYPE_AUCTION;
                    break;
                case Ess_M2ePro_Model_Ebay_Listing_Product_Action_Request_Selling::LISTING_TYPE_FIXED:
                    $result = Ess_M2ePro_Model_Ebay_Template_SellingFormat::LISTING_TYPE_FIXED;
                    break;
            }

        } else {
            $result = $listingProduct->getChildObject()->getListingType();
        }

        return $result;
    }

    //########################################

    private function logReportChange(Ess_M2ePro_Model_Listing_Product $listingProduct, $logMessage)
    {
        if (empty($logMessage)) {
            return;
        }

        $log = Mage::getModel('M2ePro/Listing_Log');
        $log->setComponentMode(Ess_M2ePro_Helper_Component_Ebay::NICK);

        $log->addProductMessage(
            $listingProduct->getListingId(),
            $listingProduct->getProductId(),
            $listingProduct->getId(),
            Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION,
            $this->getLogsActionId(),
            Ess_M2ePro_Model_Listing_Log::ACTION_CHANNEL_CHANGE,
            $logMessage,
            Ess_M2ePro_Model_Log_Abstract::TYPE_SUCCESS,
            Ess_M2ePro_Model_Log_Abstract::PRIORITY_LOW
        );
    }

    //########################################
}