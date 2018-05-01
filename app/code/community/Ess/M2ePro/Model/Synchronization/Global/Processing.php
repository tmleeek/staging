<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Synchronization_Global_Processing extends Ess_M2ePro_Model_Synchronization_Global_Abstract
{
    //####################################

    protected function getType()
    {
        return Ess_M2ePro_Model_Synchronization_Task_Global_Abstract::PROCESSING;
    }

    protected function getNick()
    {
        return NULL;
    }

    // -----------------------------------

    protected function getPercentsStart()
    {
        return 0;
    }

    protected function getPercentsEnd()
    {
        return 60;
    }

    //####################################

    protected function performActions()
    {
        $this->processExpired();
        $this->processCompleted();
    }

    //####################################

    private function processExpired()
    {
        $processingCollection = Mage::getResourceModel('M2ePro/Processing_Collection');
        $processingCollection->setOnlyExpiredItemsFilter();
        $processingCollection->addFieldToFilter('is_completed', 0);

        /** @var Ess_M2ePro_Model_Processing[] $processingObjects */
        $processingObjects = $processingCollection->getItems();

        foreach ($processingObjects as $processingObject) {

            $this->getActualLockItem()->activate();

            try {
                /** @var Ess_M2ePro_Model_Processing_Runner $processingRunner */
                $processingRunner = Mage::getModel($processingObject->getModel());
                $processingRunner->setProcessingObject($processingObject);

                $processingRunner->processExpired();
                $processingRunner->complete();
            } catch (Exception $exception) {
                $this->forceRemoveProcessing($processingObject);
                Mage::helper('M2ePro/Module_Exception')->process($exception);
            }
        }
    }

    private function processCompleted()
    {
        $processingCollection = Mage::getResourceModel('M2ePro/Processing_Collection');
        $processingCollection->addFieldToFilter('is_completed', 1);

        /** @var Ess_M2ePro_Model_Processing[] $processingObjects */
        $processingObjects = $processingCollection->getItems();

        foreach ($processingObjects as $processingObject) {

            $this->getActualLockItem()->activate();

            try {
                /** @var Ess_M2ePro_Model_Processing_Runner $processingRunner */
                $processingRunner = Mage::getModel($processingObject->getModel());
                $processingRunner->setProcessingObject($processingObject);

                $processingRunner->processSuccess() && $processingRunner->complete();
            } catch (Exception $exception) {
                $this->forceRemoveProcessing($processingObject);
                Mage::helper('M2ePro/Module_Exception')->process($exception);
            }
        }
    }

    //####################################

    private function forceRemoveProcessing(Ess_M2ePro_Model_Processing $processing)
    {
        $table = Mage::getResourceModel('M2ePro/Processing_Lock')->getMainTable();
        Mage::getSingleton('core/resource')->getConnection('core_write')->delete(
            $table, array('`processing_id` = ?' => (int)$processing->getId())
        );

        $table = Mage::getResourceModel('M2ePro/Processing')->getMainTable();
        Mage::getSingleton('core/resource')->getConnection('core_write')->delete(
            $table, array('`id` = ?' => (int)$processing->getId())
        );
    }

    //####################################
}