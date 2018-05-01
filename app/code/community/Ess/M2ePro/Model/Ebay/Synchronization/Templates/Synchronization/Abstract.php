<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Ebay_Synchronization_Templates_Synchronization_Abstract
    extends Ess_M2ePro_Model_Ebay_Synchronization_Templates_Abstract
{
    /**
     * @var Ess_M2ePro_Model_Synchronization_Templates_Synchronization_Runner
     */
    protected $runner = NULL;

    /**
     * @var Ess_M2ePro_Model_Ebay_Synchronization_Templates_Synchronization_Inspector
     */
    protected $inspector = NULL;

    /**
     * @var Ess_M2ePro_Model_Synchronization_Templates_ProductChanges_Manager
     */
    protected $productChangesManager = NULL;

    //########################################

    protected function processTask($taskPath)
    {
        return parent::processTask('Synchronization_'.$taskPath);
    }

    //########################################

    /**
     * @param Ess_M2ePro_Model_Synchronization_Templates_Synchronization_Runner $object
     */
    public function setRunner(Ess_M2ePro_Model_Synchronization_Templates_Synchronization_Runner $object)
    {
        $this->runner = $object;
    }

    /**
     * @return Ess_M2ePro_Model_Synchronization_Templates_Synchronization_Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    // ---------------------------------------

    /**
     * @param Ess_M2ePro_Model_Ebay_Synchronization_Templates_Synchronization_Inspector $object
     */
    public function setInspector(Ess_M2ePro_Model_Ebay_Synchronization_Templates_Synchronization_Inspector $object)
    {
        $this->inspector = $object;
    }

    /**
     * @return Ess_M2ePro_Model_Ebay_Synchronization_Templates_Synchronization_Inspector
     */
    public function getInspector()
    {
        return $this->inspector;
    }

    // ---------------------------------------

    /**
     * @param Ess_M2ePro_Model_Synchronization_Templates_ProductChanges_Manager $manager
     * @return $this
     */
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
}