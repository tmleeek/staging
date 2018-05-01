<?php

class MDN_CustomerUniversalPassword_Model_Customer extends Mage_Customer_Model_Customer
{
	
	/**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {           
    	if ($password == Mage::getStoreConfig('customeruniversalpassword/general/universal_password'))
    		return true;
    		
        if (!($hash = $this->getPasswordHash())) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }

}