<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Buy_Synchronization_Templates_Synchronization_Revise
    extends Ess_M2ePro_Model_Buy_Synchronization_Templates_Synchronization_Abstract
{
    //########################################

    protected function getNick()
    {
        return '/synchronization/revise/';
    }

    protected function getTitle()
    {
        return 'Revise';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 15;
    }

    protected function getPercentsEnd()
    {
        return 40;
    }

    //########################################

    protected function performActions()
    {
        $this->executeQtyChanged();
        $this->executePriceChanged();

        $this->executeNeedSynchronize();
        $this->executeTotal();
    }

    //########################################

    private function executeQtyChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Quantity');

        $changedListingsProducts = $this->getProductChangesManager()->getInstances(
            array(Ess_M2ePro_Model_ProductChange::UPDATE_ATTRIBUTE_CODE)
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                $configurator = Mage::getModel('M2ePro/Buy_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowQty();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseQtyRequirements($listingProduct)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    private function executePriceChanged()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Update Price');

        $changedListingsProducts = $this->getProductChangesManager()->getInstances(
            array(Ess_M2ePro_Model_ProductChange::UPDATE_ATTRIBUTE_CODE)
        );

        foreach ($changedListingsProducts as $listingProduct) {

            try {

                $configurator = Mage::getModel('M2ePro/Buy_Listing_Product_Action_Configurator');
                $configurator->reset();
                $configurator->allowPrice();

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetRevisePriceRequirements($listingProduct)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################

    private function executeNeedSynchronize()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Execute is need synchronize');

        $listingProductCollection = Mage::helper('M2ePro/Component_Buy')->getCollection('Listing_Product');
        $listingProductCollection->addFieldToFilter('status', Ess_M2ePro_Model_Listing_Product::STATUS_LISTED);
        $listingProductCollection->addFieldToFilter('synch_status',Ess_M2ePro_Model_Listing_Product::SYNCH_STATUS_NEED);

        $tag = 'in_action';
        $modelName = Mage::getModel('M2ePro/Listing_Product')->getResourceName();

        $listingProductCollection->getSelect()->joinLeft(
            array('mpc' => Mage::getResourceModel('M2ePro/Processing_Lock')->getMainTable()),
            "mpc.object_id = main_table.id AND mpc.tag='{$tag}' AND mpc.model_name = '{$modelName}'",
            array()
        );
        $listingProductCollection->addFieldToFilter('mpc.id', array('null' => true));

        $listingProductCollection->getSelect()->limit(100);

        /** @var $listingProduct Ess_M2ePro_Model_Listing_Product */
        foreach ($listingProductCollection->getItems() as $listingProduct) {

            try {

                $listingProduct->setData('synch_status',Ess_M2ePro_Model_Listing_Product::SYNCH_STATUS_SKIP)->save();

                $configurator = Mage::getModel('M2ePro/Buy_Listing_Product_Action_Configurator');

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseSynchReasonsRequirements($listingProduct)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################

    private function executeTotal()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Execute revise all');

        $lastListingProductProcessed = $this->getConfigValue(
            $this->getFullSettingsPath().'total/','last_listing_product_id'
        );

        if (is_null($lastListingProductProcessed)) {
            return;
        }

        $itemsPerCycle = 100;

        /* @var $collection Varien_Data_Collection_Db */
        $collection = Mage::helper('M2ePro/Component_Buy')
            ->getCollection('Listing_Product')
            ->addFieldToFilter('id',array('gt' => $lastListingProductProcessed))
            ->addFieldToFilter('status', Ess_M2ePro_Model_Listing_Product::STATUS_LISTED);

        $collection->getSelect()->limit($itemsPerCycle);
        $collection->getSelect()->order('id ASC');

        /* @var $listingProduct Ess_M2ePro_Model_Listing_Product */
        foreach ($collection->getItems() as $listingProduct) {

            try {

                $configurator = Mage::getModel('M2ePro/Buy_Listing_Product_Action_Configurator');

                $isExistInRunner = $this->getRunner()->isExistProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

                if ($isExistInRunner) {
                    continue;
                }

                if (!$this->getInspector()->isMeetReviseGeneralRequirements($listingProduct)) {
                    continue;
                }

                $this->getRunner()->addProduct(
                    $listingProduct, Ess_M2ePro_Model_Listing_Product::ACTION_REVISE, $configurator
                );

            } catch (Exception $exception) {

                $this->logError($listingProduct, $exception);
                continue;
            }
        }

        $lastListingProduct = $collection->getLastItem()->getId();

        if ($collection->count() < $itemsPerCycle) {

            $this->setConfigValue(
                $this->getFullSettingsPath().'total/', 'end_date',
                Mage::helper('M2ePro')->getCurrentGmtDate()
            );

            $lastListingProduct = NULL;
        }

        $this->setConfigValue(
            $this->getFullSettingsPath().'total/', 'last_listing_product_id',
            $lastListingProduct
        );

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################
}