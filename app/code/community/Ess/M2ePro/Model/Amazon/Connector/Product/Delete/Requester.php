<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Connector_Product_Delete_Requester
    extends Ess_M2ePro_Model_Amazon_Connector_Product_Requester
{
    // ########################################

    public function getCommand()
    {
        return array('product','delete','entities');
    }

    // ########################################

    protected function getActionType()
    {
        return Ess_M2ePro_Model_Listing_Product::ACTION_DELETE;
    }

    protected function getLockIdentifier()
    {
        $identifier = parent::getLockIdentifier();

        if (!empty($this->params['remove'])) {
            $identifier .= '_and_remove';
        }

        return $identifier;
    }

    protected function getLogsAction()
    {
        return !empty($this->params['remove']) ?
               Ess_M2ePro_Model_Listing_Log::ACTION_DELETE_AND_REMOVE_PRODUCT :
               Ess_M2ePro_Model_Listing_Log::ACTION_DELETE_PRODUCT_FROM_COMPONENT;
    }

    // ########################################

    protected function validateListingProduct()
    {
        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
        $amazonListingProduct = $this->listingProduct->getChildObject();
        $variationManager = $amazonListingProduct->getVariationManager();

        $parentListingProduct = null;

        if ($variationManager->isRelationChildType()) {
            $parentListingProduct = $variationManager->getTypeModel()->getParentListingProduct();
        }

        $validator = $this->getValidatorObject();

        $validationResult = $validator->validate();

        if (!$validationResult && $this->listingProduct->isDeleted()) {
            if (!is_null($parentListingProduct)) {
                $parentListingProduct->loadInstance($parentListingProduct->getId());

                /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonParentListingProduct */
                $amazonParentListingProduct = $parentListingProduct->getChildObject();
                $amazonParentListingProduct->getVariationManager()->getTypeModel()->getProcessor()->process();
            }

            return false;
        }

        foreach ($validator->getMessages() as $messageData) {

            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromPreparedData($messageData['text'], $messageData['type']);

            $this->getLogger()->logListingProductMessage(
                $this->listingProduct,
                $message,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM
            );
        }

        return $validationResult;
    }

    // ########################################

    protected function validateAndProcessParentListingProduct()
    {
        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
        $amazonListingProduct = $this->listingProduct->getChildObject();

        if (!$amazonListingProduct->getVariationManager()->isRelationParentType()) {
            return false;
        }

        $childListingsProducts = $amazonListingProduct->getVariationManager()
            ->getTypeModel()
            ->getChildListingsProducts();

        $filteredByStatusChildListingProducts = $this->filterChildListingProductsByStatus($childListingsProducts);
        $filteredByStatusNotLockedChildListingProducts = $this->filterLockedChildListingProducts(
            $filteredByStatusChildListingProducts
        );

        if (empty($this->params['remove']) && empty($filteredByStatusNotLockedChildListingProducts)) {
            $this->listingProduct->setData('no_child_for_processing', true);
            return false;
        }

        $notLockedChildListingProducts = $this->filterLockedChildListingProducts($childListingsProducts);

        if (count($childListingsProducts) != count($notLockedChildListingProducts)) {
            $this->listingProduct->setData('child_locked', true);
            return false;
        }

        if (!empty($this->params['remove'])) {
            $this->listingProduct->addData(array(
                'general_id'          => null,
                'is_general_id_owner' => Ess_M2ePro_Model_Amazon_Listing_Product::IS_GENERAL_ID_OWNER_NO,
                'status'              => Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED,
            ));
            $this->listingProduct->save();

            $amazonListingProduct->getVariationManager()->switchModeToAnother();

            $this->listingProduct->deleteInstance();
        }

        if (empty($filteredByStatusNotLockedChildListingProducts)) {
            return true;
        }

        $childListingsProductsIds = array();
        foreach ($filteredByStatusNotLockedChildListingProducts as $listingProduct) {
            $childListingsProductsIds[] = $listingProduct->getId();
        }

        /** @var Ess_M2ePro_Model_Mysql4_Listing_Product_Collection $listingProductCollection */
        $listingProductCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Listing_Product');
        $listingProductCollection->addFieldToFilter('id', array('in' => $childListingsProductsIds));

        /** @var Ess_M2ePro_Model_Listing_Product[] $processChildListingsProducts */
        $processChildListingsProducts = $listingProductCollection->getItems();
        if (empty($processChildListingsProducts)) {
            return true;
        }

        $dispatcherParams = array_merge($this->params, array('is_parent_action' => true));

        $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Product_Dispatcher');
        $processStatus = $dispatcherObject->process(
            $this->getActionType(), $processChildListingsProducts, $dispatcherParams
        );

        if ($processStatus == Ess_M2ePro_Helper_Data::STATUS_ERROR) {
            $this->getLogger()->setStatus(Ess_M2ePro_Helper_Data::STATUS_ERROR);
        }

        return true;
    }

    // ########################################

    /**
     * @param Ess_M2ePro_Model_Listing_Product[] $listingProducts
     * @return Ess_M2ePro_Model_Listing_Product[]
     */
    protected function filterChildListingProductsByStatus(array $listingProducts)
    {
        $resultListingProducts = array();

        foreach ($listingProducts as $id => $childListingProduct) {
            if ($childListingProduct->isBlocked() && empty($this->params['remove'])) {
                continue;
            }

            $resultListingProducts[] = $childListingProduct;
        }

        return $resultListingProducts;
    }

    protected function isListingProductLocked()
    {
        if (parent::isListingProductLocked()) {
            return true;
        }

        if (empty($this->params['remove'])) {
            return false;
        }

        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
        $amazonListingProduct = $this->listingProduct->getChildObject();

        if (!$amazonListingProduct->getVariationManager()->isRelationParentType()) {
            return false;
        }

        if (!$this->listingProduct->isSetProcessingLock('child_products_in_action')) {
            return false;
        }

        // M2ePro_TRANSLATIONS
        // Another Action is being processed. Try again when the Action is completed.
        $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
        $message->initFromPreparedData(
            'Delete and Remove action is not supported if Child Products are in Action.',
            Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
        );

        $this->getLogger()->logListingProductMessage(
            $this->listingProduct,
            $message,
            Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM
        );

        return true;
    }

    // ########################################
}