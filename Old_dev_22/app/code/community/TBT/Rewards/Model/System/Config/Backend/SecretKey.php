<?php

class TBT_Rewards_Model_System_Config_Backend_SecretKey extends Mage_Adminhtml_Model_System_Config_Backend_Encrypted
{

    /** If the api key is set by the account creation routine, then accept that value
     *  and don't overwrite it with the blank value that will probably be set on 
     *  the field value.
     */
    public function _beforeSave()
    {
        $value = Mage::helper('rewards/config')->getConfigValue('rewards/platform/secretkey');
        if ($value) {
            $this->setValue($value);
        }    
        
        return parent::_beforeSave();
    }
}
