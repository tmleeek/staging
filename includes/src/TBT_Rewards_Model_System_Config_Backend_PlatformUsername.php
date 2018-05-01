<?php

class TBT_Rewards_Model_System_Config_Backend_PlatformUsername extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
        if ($this->getValue() || $this->getFieldsetDataValue('email')) {
            $this->_createPlatformAccount();
        }

        return parent::_afterSave();
    }

    protected function _createPlatformAccount()
    {
        $username = $this->getValue();
        $password = $this->getFieldsetDataValue('password');
        $devMode  = $this->getFieldsetDataValue('dev_mode');

        if (Mage::getStoreConfig('rewards/platform/is_signup')) {
            $email = $this->getFieldsetDataValue('email');

            if (!$username && !$password && !$email) {
                return $this;
            }

            Mage::helper('rewards/platform')->createPlatformAccount($username, $email, $password);
        } else {
            if (!$username && !$password) {
                return $this;
            }

            Mage::helper('rewards/platform')->connectWithPlatformAccount($username, $password, $devMode);
        }

        return $this;
    }

}
