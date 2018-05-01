<?php

try {
    include_once(Mage::getBaseDir('lib'). DS. 'SweetTooth'. DS .'SweetTooth.php');
} catch (Exception $e) {
    die("Wasn't able to load lib/SweetTooth.php.  Download rewardsplatformsdk.git and run the installer to symlink it.");  
}

class TBT_Rewards_Model_System_Config_Backend_ApiKey extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
        // The api key will only be set here in the event that the account was just created
        // and in that event we don't need to validate the creds b/c they were just pulled 
        // down from platform.
        $api_key = Mage::helper('rewards/config')->getConfigValue('rewards/platform/apikey');
        if (!$api_key) {
            $this->_validateApiCreds();
        }
        
        return parent::_afterSave();
    }
    
    /** If the api key is set by the account creation routine, then accept that value
     *  and don't overwrite it with the blank value that will probably be set on 
     *  the apiKey field.
     */
    public function save()
    {
        $api_key = Mage::helper('rewards/config')->getConfigValue('rewards/platform/apikey');
        if ($api_key) {
            $this->setValue($api_key);
        }    

        return parent::save();
    }
    
    /**
     * 
     */
    protected function _validateApiCreds() {
        if ($this->isValueChanged() ||
                Mage::helper('core')->encrypt($this->getFieldsetDataValue('secretkey')) != $this->getOldValue('secretkey')) {
            $this->_checkLicenseOverServer($this->getValue(), $this->getFieldsetDataValue('secretkey'));
            Mage::getConfig()->saveConfig('rewards/platform/is_connected', 1);
        } else {
            if($this->getValue() && $this->getFieldsetDataValue('secretkey')) {
                return $this;
            } else {
                Mage::getSingleton('core/session')->addWarning("Don't forget to supply your Sweet Tooth credentials before you can setup your rewards campaign!");
                Mage::getConfig()->saveConfig('rewards/platform/is_connected', 0);
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param unknown_type $apiKey
     * @param unknown_type $secretKey
     * @throws Exception
     */
    protected function _checkLicenseOverServer($apiKey, $secretKey) {
        
        try {
            
            $stp = new SweetTooth($apiKey, $secretKey);
        
            $stp->authenticate();
            
        } catch(Exception $e) {
            Mage::getConfig()->saveConfig('rewards/platform/is_connected', 0);
            
            if($e->getCode() == 403) {
                throw new Exception(Mage::helper('rewards')->__('The api details you provided were not accepted by the server. Either you entered the keys in wrong, your account is inactive. For more help, please contact the Sweet Tooth support team.'));
            } elseif($e->getCode() % 100 == 5) {
                throw new Exception(Mage::helper('rewards')->__('A problem occured on the server while trying to authenticate your credentials.  Please contact our support team with the following info: ') . $e->getMessage());
            } else {
                throw new Exception(Mage::helper('rewards')->__('An unknown problem occured while trying to authenticate your credentials. Please contact our support team with the following info: ') . $e->getMessage());
            }
            
        }
        
        Mage::getSingleton('core/session')->addSuccess("API credentials have been validated.  You may now continue configuring your rewards campaign.");
        
        return $this;
    }
    
    /**
     * Check if config data value was changed
     *
     * @return bool
     */
    public function isValueChanged($key = null)
    {
        if ($key) {
            return $this->getFieldsetDataValue($key) != $this->getOldValue($key);
        }
        
        return $this->getValue() != $this->getOldValue();
    }
    
    /**
     * Get old value from existing config
     *
     * @return string
     */
    public function getOldValue($key = null)
    {
        $storeCode   = $this->getStoreCode();
        $websiteCode = $this->getWebsiteCode();
        $path        = $key == null ? $this->getPath() : str_replace($this->getField(), $key, $this->getPath());
        
        if ($storeCode) {
            return Mage::app()->getStore($storeCode)->getConfig($path);
        }
        if ($websiteCode) {
            return Mage::app()->getWebsite($websiteCode)->getConfig($path);
        }
        return (string) Mage::getConfig()->getNode('default/' . $path);
    }
}
