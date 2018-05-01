<?php

class TBT_Rewards_Model_System_Config_Platform_Sync_Enable extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
        /** @var $webservice TBT_Rewards_Model_Platform_WebService */
        $webservice = Mage::getModel('rewards/platform_webService');

        if ($this->getValue() && !$webservice->isRoleCreated()) {
            $this->_setupApi();
        }

        return parent::_afterSave();
    }

    protected function _setupApi()
    {
        try {
            return $this->_executeSetupApi();
        } catch (Exception $e) {
            $this->_error($e);
        }
    }

    protected function _executeSetupApi()
    {
        /** @var $webservice TBT_Rewards_Model_Platform_WebService */
        /** @var $client TBT_Rewards_Model_Platform_Instance */
        $client = Mage::getSingleton('rewards/platform_instance');
        $webservice = Mage::getModel('rewards/platform_webService');

        $webservice->setClient($client)
            ->setup();
        Mage::getConfig()->cleanCache();

        $this->_success("SweetToothApp.com data sync has been successfully enabled.");

        return $this;
    }

    protected function _error(Exception $exception)
    {
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');
        $session->addError("Problem setting up SweetToothApp.com data sync: " . $exception->getMessage());
        Mage::logException($exception);

        return $this;
    }

    protected function _success($message)
    {
        /** @var $session Mage_Core_Model_Session */
        $session = Mage::getSingleton('core/session');
        $session->addSuccess($message);

        return $this;
    }

}