<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

abstract class Ess_M2ePro_Model_Cron_Checker_Task_Abstract
{
    //########################################

    /**
     * @return string
     */
    abstract protected function getNick();

    abstract public function performActions();

    //########################################

    public function process()
    {
        if (!$this->isPossibleToRun()) {
            return;
        }

        $this->updateLastRun();

        $this->performActions();
    }

    //########################################

    /**
     * @return bool
     */
    public function isPossibleToRun()
    {
        return $this->isIntervalExceeded();
    }

    /**
     * @return bool
     */
    protected function isIntervalExceeded()
    {
        $lastRun = $this->getConfigValue('last_run');

        if (is_null($lastRun)) {
            return true;
        }

        $interval = (int)$this->getConfigValue('interval');
        $currentTimeStamp = Mage::helper('M2ePro')->getCurrentGmtDate(true);

        return $currentTimeStamp > strtotime($lastRun) + $interval;
    }

    // ---------------------------------------

    protected function updateLastRun()
    {
        $this->setConfigValue('last_run', Mage::helper('M2ePro')->getCurrentGmtDate());
    }

    //########################################

    private function getConfig()
    {
        return Mage::helper('M2ePro/Module')->getConfig();
    }

    private function getConfigGroup()
    {
        return '/cron/checker/task/'.$this->getNick().'/';
    }

    // ---------------------------------------

    private function getConfigValue($key)
    {
        return $this->getConfig()->getGroupValue($this->getConfigGroup(), $key);
    }

    private function setConfigValue($key, $value)
    {
        return $this->getConfig()->setGroupValue($this->getConfigGroup(), $key, $value);
    }

    //########################################
}