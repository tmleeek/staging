<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

final class Ess_M2ePro_Model_Amazon_Synchronization_Templates_Synchronization
    extends Ess_M2ePro_Model_Amazon_Synchronization_Templates_Abstract
{
    /**
     * @var Ess_M2ePro_Model_Synchronization_Templates_Synchronization_Runner
     */
    private $runner = NULL;

    /**
     * @var Ess_M2ePro_Model_Amazon_Synchronization_Templates_Synchronization_Inspector
     */
    private $inspector = NULL;

    //########################################

    /**
     * @return Ess_M2ePro_Model_Synchronization_Templates_Synchronization_Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    // ---------------------------------------

    /**
     * @return Ess_M2ePro_Model_Amazon_Synchronization_Templates_Synchronization_Inspector
     */
    public function getInspector()
    {
        return $this->inspector;
    }

    //########################################

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
        return 20;
    }

    protected function getPercentsEnd()
    {
        return 100;
    }

    //########################################

    protected function beforeStart()
    {
        parent::beforeStart();

        $this->runner = Mage::getModel('M2ePro/Synchronization_Templates_Synchronization_Runner');

        $this->runner->setConnectorModel('Amazon_Connector_Product_Dispatcher');
        $this->runner->setMaxProductsPerStep(100);

        $this->runner->setLockItem($this->getActualLockItem());
        $this->runner->setPercentsStart($this->getPercentsStart() + $this->getPercentsInterval()/2);
        $this->runner->setPercentsEnd($this->getPercentsEnd());

        $this->inspector = Mage::getModel('M2ePro/Amazon_Synchronization_Templates_Synchronization_Inspector');
    }

    protected function afterEnd()
    {
        $this->executeRunner();
        parent::afterEnd();
    }

    // ---------------------------------------

    protected function performActions()
    {
        $result = true;

        $result = !$this->processTask('Synchronization_List') ? false : $result;
        $result = !$this->processTask('Synchronization_Revise') ? false : $result;
        $result = !$this->processTask('Synchronization_Relist') ? false : $result;
        $result = !$this->processTask('Synchronization_Stop') ? false : $result;

        return $result;
    }

    protected function makeTask($taskPath)
    {
        $task = parent::makeTask($taskPath);

        $task->setRunner($this->getRunner());
        $task->setInspector($this->getInspector());
        $task->setProductChangesManager($this->getProductChangesManager());

        return $task;
    }

    //########################################

    private function executeRunner()
    {
        $this->getActualOperationHistory()->addTimePoint(__METHOD__,'Apply Products changes on Amazon');

        $this->getRunner()->execute();

        $this->getActualOperationHistory()->saveTimePoint(__METHOD__);
    }

    //########################################
}