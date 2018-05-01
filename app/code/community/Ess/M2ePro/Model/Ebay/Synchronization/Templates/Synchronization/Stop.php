<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Ebay_Synchronization_Templates_Synchronization_Stop
    extends Ess_M2ePro_Model_Ebay_Synchronization_Templates_Synchronization_Abstract
{
    //########################################

    /**
     * @return string
     */
    protected function getNick()
    {
        return '/synchronization/stop/';
    }

    /**
     * @return string
     */
    protected function getTitle()
    {
        return 'Stop';
    }

    // ---------------------------------------

    /**
     * @return int
     */
    protected function getPercentsStart()
    {
        return 80;
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
        $this->immediatelyChangedProducts();
    }

    //########################################

    private function immediatelyChangedProducts()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Immediately when Product was changed');

        $changedListingsProducts = $this->getProductChangesManager()->getInstances(
            array(Ess_M2ePro_Model_ProductChange::UPDATE_ATTRIBUTE_CODE)
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                $action = $this->getAction($listingProduct);

                /** @var $configurator Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Ebay_Listing_Product_Action_Configurator');

                $this->prepareConfigurator($listingProduct, $configurator, $action);

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, $action, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetStopRequirements($listingProduct)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, $action, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################

    private function getAction(Ess_M2ePro_Model_Listing_Product $listingProduct)
    {
        /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
        $ebayListingProduct = $listingProduct->getChildObject();

        if (!$ebayListingProduct->isOutOfStockControlEnabled()) {
            return Ess_M2ePro_Model_Listing_Product::ACTION_STOP;
        }

        return Ess_M2ePro_Model_Listing_Product::ACTION_REVISE;
    }

    private function prepareConfigurator(Ess_M2ePro_Model_Listing_Product $listingProduct,
                                         Ess_M2ePro_Model_Ebay_Listing_Product_Action_Configurator $configurator,
                                         $action)
    {
        if ($action != Ess_M2ePro_Model_Listing_Product::ACTION_STOP) {
            $configurator->setParams(array('replaced_action' => Ess_M2ePro_Model_Listing_Product::ACTION_STOP));
        }

        /** @var Ess_M2ePro_Model_Ebay_Listing_Product $ebayListingProduct */
        $ebayListingProduct = $listingProduct->getChildObject();

        if (!$ebayListingProduct->isOutOfStockControlEnabled() &&
            $action == Ess_M2ePro_Model_Listing_Product::ACTION_STOP
        ) {
            return;
        }

        $configurator->reset();
        $configurator->allowQty()->allowVariations();
    }

    //########################################
}