<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Buy_Synchronization_Templates_Abstract
    extends Ess_M2ePro_Model_Buy_Synchronization_Abstract
{
    /**
     * @var Ess_M2ePro_Model_Synchronization_Templates_ProductChanges_Manager
     */
    protected $productChangesManager = NULL;

    //########################################

    public function setProductChangesManager(Ess_M2ePro_Model_Synchronization_Templates_ProductChanges_Manager $manager)
    {
        $this->productChangesManager = $manager;
        return $this;
    }

    /**
     * @return Ess_M2ePro_Model_Synchronization_Templates_ProductChanges_Manager
     */
    public function getProductChangesManager()
    {
        return $this->productChangesManager;
    }

    //########################################

    protected function getType()
    {
        return Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::TEMPLATES;
    }

    protected function processTask($taskPath)
    {
        return parent::processTask('Templates_'.$taskPath);
    }

    //########################################

    protected function logError(Ess_M2ePro_Model_Listing_Product $listingProduct, Exception $exception)
    {
        /** @var Ess_M2ePro_Model_Buy_Listing_Log $logModel */
        $logModel = Mage::getModel('M2ePro/Buy_Listing_Log');

        $logModel->addProductMessage(
            $listingProduct->getListingId(),
            $listingProduct->getProductId(),
            $listingProduct->getId(),
            Ess_M2ePro_Helper_Data::INITIATOR_EXTENSION,
            $logModel->getNextActionId(),
            $this->getActionForLog(),
            $exception->getMessage(),
            Ess_M2ePro_Model_Log_Abstract::TYPE_ERROR,
            Ess_M2ePro_Model_Log_Abstract::PRIORITY_HIGH
        );

        Mage::helper('M2ePro/Module_Exception')->process($exception);
    }

    //########################################
}