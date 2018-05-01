<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

/**
 * @method Ess_M2ePro_Model_Amazon_Listing_Product_Action_Type_List_Response getResponseObject()
 */

class Ess_M2ePro_Model_Amazon_Connector_Product_List_Responser
    extends Ess_M2ePro_Model_Amazon_Connector_Product_Responser
{
    // ########################################

    protected function getSuccessfulMessage()
    {
        // M2ePro_TRANSLATIONS
        // Item was successfully Listed
        return 'Item was successfully Listed';
    }

    // ########################################

    protected function inspectProduct()
    {
        parent::inspectProduct();

        $runner = Mage::getModel('M2ePro/Synchronization_Templates_Synchronization_Runner');
        $runner->setConnectorModel('Amazon_Connector_Product_Dispatcher');
        $runner->setMaxProductsPerStep(100);

        if (!$this->isSuccess) {
            if ($this->listingProduct->needSynchRulesCheck()) {
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');

                $responseData = $this->getPreparedResponseData();
                if (empty($responseData['request_time']) && !empty($responseData['start_processing_date'])) {
                    $configurator->setParams(array('start_processing_date' => $responseData['start_processing_date']));
                }

                $runner->addProduct(
                    $this->listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_LIST, $configurator
                );
                $runner->execute();
            }

            return;
        }

        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
        $amazonListingProduct = $this->listingProduct->getChildObject();

        if (!$amazonListingProduct->getVariationManager()->isRelationParentType()) {
            return;
        }

        $childListingProducts = $amazonListingProduct->getVariationManager()
            ->getTypeModel()
            ->getChildListingsProducts();

        if (empty($childListingProducts)) {
            return;
        }

        $inspector = Mage::getModel('M2ePro/Amazon_Synchronization_Templates_Synchronization_Inspector');

        foreach ($childListingProducts as $listingProduct) {

            if (!$inspector->isMeetListRequirements($listingProduct)) {
                continue;
            }

            $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');

            $runner->addProduct(
                $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_LIST, $configurator
            );
        }

        $runner->execute();
    }

    // ########################################

    protected function processSuccess(array $params = array())
    {
        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
        $amazonListingProduct = $this->listingProduct->getChildObject();

        if ($amazonListingProduct->getVariationManager()->isRelationMode() &&
            !$this->getRequestDataObject()->hasProductId() &&
            empty($params['general_id'])
        ) {
            $message = Mage::getModel('M2ePro/Connector_Connection_Response_Message');
            $message->initFromPreparedData(
                'Unexpected error. The ASIN/ISBN for Parent or Child Product was not returned from Amazon.
                 Operation cannot be finished correctly.',
                Ess_M2ePro_Model_Connector_Connection_Response_Message::TYPE_ERROR
            );

            $this->getLogger()->logListingProductMessage(
                $this->listingProduct,
                $message,
                Ess_M2ePro_Model_Log_Abstract::PRIORITY_MEDIUM
            );

            return;
        }

        parent::processSuccess($params);
    }

    protected function getSuccessfulParams()
    {
        $responseData = $this->getPreparedResponseData();

        if (empty($responseData['asins'])) {
            return array();
        }

        return array('general_id' => $responseData['asins']);
    }

    // ########################################
}