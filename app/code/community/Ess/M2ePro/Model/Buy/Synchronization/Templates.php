<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Buy_Synchronization_Templates
    extends Ess_M2ePro_Model_Buy_Synchronization_Abstract
{
    /** @var Ess_M2ePro_Model_Synchronization_Templates_ProductChanges_Manager $productChangesManager */
    private $productChangesManager = NULL;

    //########################################

    protected function getProductChangesManager()
    {
        return $this->productChangesManager;
    }

    //########################################

    protected function getType()
    {
        return Ess_M2ePro_Model_Synchronization_Task_Component_Abstract::TEMPLATES;
    }

    protected function getNick()
    {
        return NULL;
    }

    protected function getTitle()
    {
        return 'Inventory';
    }

    // ---------------------------------------

    protected function getPercentsStart()
    {
        return 0;
    }

    protected function getPercentsEnd()
    {
        return 100;
    }

    //########################################

    protected function beforeStart()
    {
        parent::beforeStart();

        $this->productChangesManager = Mage::getModel('M2ePro/Synchronization_Templates_ProductChanges_Manager');
        $this->productChangesManager->setComponent($this->getComponent());
        $this->productChangesManager->init();
    }

    protected function afterEnd()
    {
        parent::afterEnd();
        $this->getProductChangesManager()->clearCache();
    }

    // ---------------------------------------

    protected function performActions()
    {
        $result = true;

        $result = !$this->processTask('Templates_Synchronization') ? false : $result;

        return $result;
    }

    protected function makeTask($taskPath)
    {
        $task = parent::makeTask($taskPath);
        $task->setProductChangesManager($this->getProductChangesManager());

        return $task;
    }

    //########################################
}