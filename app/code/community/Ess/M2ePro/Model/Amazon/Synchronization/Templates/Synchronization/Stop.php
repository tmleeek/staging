<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Amazon_Synchronization_Templates_Synchronization_Stop
    extends Ess_M2ePro_Model_Amazon_Synchronization_Templates_Synchronization_Abstract
{
    //########################################

    protected function getNick()
    {
        return '/synchronization/stop/';
    }

    protected function getTitle()
    {
        return 'Stop';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 65;
    }

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

                /** @var $configurator Ess_M2ePro_Model_Amazon_Listing_Product_Action_Configurator */
                $configurator = Mage::getModel('M2ePro/Amazon_Listing_Product_Action_Configurator');

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_STOP, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetStopRequirements($listingProduct)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_STOP, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################
}