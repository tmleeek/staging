<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Buy_Synchronization_ListingsProducts_Update_Responser
    extends Ess_M2ePro_Model_Buy_Connector_Inventory_Get_ItemsResponser
{
    protected $logsActionId = NULL;
    protected $synchronizationLog = NULL;

    //########################################

    protected function processResponseMessages()
    {
        parent::processResponseMessages();

        foreach ($this->getResponse()->getMessages()->getEntities() as $message) {

            if (!$message->isError() && !$message->isWarning()) {
                continue;
            }

            $logType = $message->isError() ? Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR
                : Ess_M2ePro_Model_Log_Abstract::TYPE_WARNING;

            $this->getSynchronizationLog()->addMessage(
                Mage::helper('M2ePro')->__($message->getText()),
                $logType,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
            );
        }
    }

    protected function isNeedProcessResponse()
    {
        if (!parent::isNeedProcessResponse()) {
            return false;
        }

        if ($this->getResponse()->getMessages()->hasErrorEntities()) {
            return false;
        }

        return true;
    }

    //########################################

    public function failDetected($messageText)
    {
        parent::failDetected($messageText);

        $this->getSynchronizationLog()->addMessage(
            Mage::helper('M2ePro')->__($messageText),
            Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
            Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
        );
    }

    //########################################

    protected function processResponseData()
    {
        // TODO
        // Note. Synchronization was temporary disabled in configs also! At this moment, Rakuten.com does not update
        // the inventory report on its FTP, so we can't get correct inventory information

        return;

        try {

            $this->updateReceivedListingsProducts();

        } catch (Exception $exception) {

            Mage::helper('M2ePro/Module_Exception')->process($exception);

            $this->getSynchronizationLog()->addMessage(
                Mage::helper('M2ePro')->__($exception->getMessage()),
                Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
            );
        }
    }

    //########################################

    protected function updateReceivedListingsProducts()
    {
        /** @var $stmtTemp Zend_Db_Statement_Pdo */
        $stmtTemp = $this->getPdoStatementExistingListings(true);

        $tempLog = Mage::getModel('M2ePro/Listing_Log');
        $tempLog->setComponentMode(Ess_M2ePro_Helper_Component_Buy::NICK);

        $responseData = $this->getPreparedResponseData();

        while ($existingItem = $stmtTemp->fetch()) {

            if (!isset($responseData[$existingItem['sku']])) {
                continue;
            }

            $receivedItem = $responseData[$existingItem['sku']];

            $newData = array(
                'general_id'              => (int)$receivedItem['general_id'],
                'online_price'            => (float)$receivedItem['price'],
                'online_qty'              => (int)$receivedItem['qty'],
                'condition'               => (int)$receivedItem['condition'],
                'condition_note'          => (string)$receivedItem['condition_note'],
                'shipping_standard_rate'  => (float)$receivedItem['shipping_standard_rate'],
                'shipping_expedited_mode' => (int)$receivedItem['shipping_expedited_mode'],
                'shipping_expedited_rate' => (float)$receivedItem['shipping_expedited_rate']
            );

            if ($newData['online_qty'] > 0) {
                $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_LISTED;
            } else {
                $newData['status'] = Ess_M2ePro_Model_Listing_Product::STATUS_STOPPED;
            }

            $existingData = array(
                'general_id'              => (int)$existingItem['general_id'],
                'online_price'            => (float)$existingItem['online_price'],
                'online_qty'              => (int)$existingItem['online_qty'],
                'condition'               => (int)$existingItem['condition'],
                'condition_note'          => (string)$existingItem['condition_note'],
                'shipping_standard_rate'  => (float)$existingItem['shipping_standard_rate'],
                'shipping_expedited_mode' => (int)$existingItem['shipping_expedited_mode'],
                'shipping_expedited_rate' => (float)$existingItem['shipping_expedited_rate'],
                'status'                  => (int)$existingItem['status']
            );

            $existingAdditionalData = Mage::helper('M2ePro')->jsonDecode($existingItem['additional_data']);

            if (!empty($existingAdditionalData['last_synchronization_dates']['qty']) &&
                !empty($this->params['request_date'])
            ) {
                $lastQtySynchDate = $existingAdditionalData['last_synchronization_dates']['qty'];

                if ($this->isProductInfoOutdated($lastQtySynchDate)) {
                    unset($newData['online_qty'], $newData['status']);
                    unset($existingData['online_qty'], $existingData['status']);
                }
            }

            if (!empty($existingAdditionalData['last_synchronization_dates']['price']) &&
                !empty($this->params['request_date'])
            ) {
                $lastPriceSynchDate = $existingAdditionalData['last_synchronization_dates']['price'];

                if ($this->isProductInfoOutdated($lastPriceSynchDate)) {
                    unset($newData['online_price']);
                    unset($existingData['online_price']);
                }
            }

            if ($newData == $existingData) {
                continue;
            }

            if ((isset($newData['status']) && $newData['status'] != $existingItem['status']) ||
                (isset($newData['online_qty']) && $newData['online_qty'] != $existingItem['online_qty']) ||
                (isset($newData['online_price']) && $newData['online_price'] != $existingItem['online_price'])
            ) {
                Mage::getModel('M2ePro/ProductChange')->addUpdateAction(
                    $existingItem['product_id'], Ess_M2ePro_Model_ProductChange::INITIATOR_SYNCHRONIZATION
                );
            }

            $tempLogMessages = array();

            if (isset($newData['online_price']) && $newData['online_price'] != $existingData['online_price']) {
                // M2ePro_TRANSLATIONS
                // Item Price was successfully changed from %from% to %to% .
                $tempLogMessages[] = Mage::helper('M2ePro')->__(
                    'Item Price was successfully changed from %from% to %to% .',
                    $existingItem['online_price'],
                    $newData['online_price']
                );
            }

            if (isset($newData['online_qty']) && $newData['online_qty'] != $existingData['online_qty']) {
                // M2ePro_TRANSLATIONS
                // Item QTY was successfully changed from %from% to %to% .
                $tempLogMessages[] = Mage::helper('M2ePro')->__(
                    'Item QTY was successfully changed from %from% to %to% .',
                    $existingItem['online_qty'],
                    $newData['online_qty']
                );
            }

            if (isset($newData['status']) && $newData['status'] != $existingData['status']) {

                $newData['status_changer'] = Ess_M2ePro_Model_Listing_Product::STATUS_CHANGER_COMPONENT;

                $statusChangedFrom = Mage::helper('M2ePro/Component_Buy')
                    ->getHumanTitleByListingProductStatus($existingData['status']);
                $statusChangedTo = Mage::helper('M2ePro/Component_Buy')
                    ->getHumanTitleByListingProductStatus($newData['status']);

                if (!empty($statusChangedFrom) && !empty($statusChangedTo)) {
                    // M2ePro_TRANSLATIONS
                    // Item Status was successfully changed from "%from%" to "%to%" .
                    $tempLogMessages[] = Mage::helper('M2ePro')->__(
                        'Item Status was successfully changed from "%from%" to "%to%" .',
                        $statusChangedFrom,
                        $statusChangedTo
                    );
                }
            }

            foreach ($tempLogMessages as $tempLogMessage) {
                $tempLog->addProductMessage(
                    $existingItem['listing_id'],
                    $existingItem['product_id'],
                    $existingItem['listing_product_id'],
                    Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION,
                    $this->getLogsActionId(),
                    Ess_M2ePro_Model_Listing_Log::ACTION_CHANNEL_CHANGE,
                    $tempLogMessage,
                    Ess_M2ePro_Model_Log_Abstract::TYPE_SUCCESS,
                    Ess_M2ePro_Model_Log_Abstract::PRIORITY_LOW
                );
            }

            $listingProductObj = Mage::helper('M2ePro/Component_Buy')
                                    ->getObject('Listing_Product',(int)$existingItem['listing_product_id']);

            $listingProductObj->addData($newData)->save();
        }
    }

    //########################################

    protected function getPdoStatementExistingListings($withData = false)
    {
        /** @var $connRead Varien_Db_Adapter_Pdo_Mysql */
        $connRead = Mage::getSingleton('core/resource')->getConnection('core_read');

        $listingTable = Mage::getResourceModel('M2ePro/Listing')->getMainTable();

        /** @var $collection Varien_Data_Collection_Db */
        $collection = Mage::helper('M2ePro/Component_Buy')->getCollection('Listing_Product');
        $collection->getSelect()->join(array('l' => $listingTable), 'main_table.listing_id = l.id', array());
        $collection->getSelect()->where('l.account_id = ?',(int)$this->getAccount()->getId());
        $collection->getSelect()->where('`main_table`.`status` != ?',
                                        (int)Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED);
        $collection->getSelect()->where("`second_table`.`sku` is not null and `second_table`.`sku` != ''");

        $dbSelect = $collection->getSelect();

        $tempColumns = array('second_table.sku');

        if ($withData) {
            $tempColumns = array('main_table.listing_id',
                                 'main_table.product_id','main_table.status',
                                 'main_table.additional_data',
                                 'second_table.sku','second_table.general_id',
                                 'second_table.online_price','second_table.online_qty',
                                 'second_table.condition','second_table.condition_note',
                                 'second_table.shipping_standard_rate',
                                 'second_table.shipping_expedited_mode',
                                 'second_table.shipping_expedited_rate',
                                 'second_table.listing_product_id');
        }

        $dbSelect->reset(Zend_Db_Select::COLUMNS)->columns($tempColumns);

        /** @var $stmtTemp Zend_Db_Statement_Pdo */
        $stmtTemp = $connRead->query($dbSelect->__toString());

        return $stmtTemp;
    }

    //########################################

    /**
     * @return Ess_M2ePro_Model_Account
     */
    protected function getAccount()
    {
        return $this->getObjectByParam('Account','account_id');
    }

    // ---------------------------------------

    protected function getLogsActionId()
    {
        if (!is_null($this->logsActionId)) {
            return $this->logsActionId;
        }

        return $this->logsActionId = Mage::getModel('M2ePro/Listing_Log')->getNextActionId();
    }

    protected function getSynchronizationLog()
    {
        if (!is_null($this->synchronizationLog)) {
            return $this->synchronizationLog;
        }

        /** @var $logs Ess_M2ePro_Model_Synchronization_Log */
        $this->synchronizationLog = Mage::getModel('M2ePro/Synchronization_Log');
        $this->synchronizationLog->setComponentMode(Ess_M2ePro_Helper_Component_Buy::NICK);
        $this->synchronizationLog->setSynchronizationTask(Ess_M2ePro_Model_Synchronization_Log::TASK_LISTINGS_PRODUCTS);

        return $this->synchronizationLog;
    }

    //########################################

    private function isProductInfoOutdated($lastDate)
    {
        $lastDate = new DateTime($lastDate, new DateTimeZone('UTC'));
        $requestDate = new DateTime($this->params['request_date'], new DateTimeZone('UTC'));

        $lastDate->modify('+1 day');

        return $lastDate > $requestDate;
    }

    // ########################################
}