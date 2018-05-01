<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Model_Amazon_Connector_Product_Delete_Responser
    extends Ess_M2ePro_Model_Amazon_Connector_Product_Responser
{
    /** @var Ess_M2ePro_Model_Listing_Product $parentForProcessing */
    protected $parentForProcessing = NULL;

    // ########################################

    protected function getSuccessfulMessage()
    {
        // M2ePro_TRANSLATIONS
        // Item was successfully Deleted
        return 'Item was successfully Deleted';
    }

    // ########################################

    public function eventAfterExecuting()
    {
        $responseData = $this->getPreparedResponseData();

        if (!empty($this->params['params']['remove']) && !empty($responseData['request_time'])) {
            /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
            $amazonListingProduct = $this->listingProduct->getChildObject();

            $variationManager = $amazonListingProduct->getVariationManager();

            if ($variationManager->isRelationChildType()) {
                $childTypeModel = $variationManager->getTypeModel();

                $parentListingProduct = $childTypeModel->getParentListingProduct();
                $this->parentForProcessing = $parentListingProduct;

                if ($childTypeModel->isVariationProductMatched()) {
                    $parentAmazonListingProduct = $childTypeModel->getAmazonParentListingProduct();

                    $parentAmazonListingProduct->getVariationManager()->getTypeModel()->addRemovedProductOptions(
                        $childTypeModel->getProductOptions()
                    );
                }
            }

            $this->listingProduct->setData('status', Ess_M2ePro_Model_Listing_Product::STATUS_NOT_LISTED);
            $this->listingProduct->save();
            $this->listingProduct->deleteInstance();
        }

        parent::eventAfterExecuting();
    }

    protected function inspectProduct()
    {
        if (empty($this->params['params']['remove'])) {
            parent::inspectProduct();
            return;
        }

        $responseData = $this->getPreparedResponseData();
        if (!empty($responseData['request_time'])) {
            return;
        }

        $configurator = $this->getConfigurator();
        if (!empty($responseData['start_processing_date'])) {
            $configurator->setParams(array('start_processing_date' => $responseData['start_processing_date']));
        }

        $dispatcherObject = Mage::getModel('M2ePro/Amazon_Connector_Product_Dispatcher');
        $dispatcherObject->process(
            Ess_M2ePro_Model_Listing_Product::ACTION_DELETE,
            array($this->listingProduct->getId()),
            $this->params['params']
        );
    }

    protected function processParentProcessor()
    {
        if (empty($this->params['params']['remove'])) {
            parent::processParentProcessor();
            return;
        }

        if (is_null($this->parentForProcessing)) {
            return;
        }

        /** @var Ess_M2ePro_Model_Amazon_Listing_Product $amazonListingProduct */
        $amazonListingProduct = $this->listingProduct->getChildObject();
        $amazonListingProduct->getVariationManager()->getTypeModel()->getProcessor()->process();
    }

    // ########################################
}