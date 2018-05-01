<?php

class TBT_Rewards_Helper_Platform_Account extends Mage_Core_Helper_Abstract
{
    const XPATH_PLATFORM_USERNAME  = 'rewards/platform/username';
    const XPATH_PLATFORM_EMAIL     = 'rewards/platform/email';
    const XPATH_PLATFORM_FIRSTNAME = 'rewards/platform/firstname';
    const XPATH_PLATFORM_LASTNAME  = 'rewards/platform/lastname';
    const XPATH_PLATFORM_OPTIONS   = 'rewards/platform/options';

    public function getAccountUsername()
    {
        return Mage::getStoreConfig(self::XPATH_PLATFORM_USERNAME);
    }

    public function getAccountEmail()
    {
        return Mage::getStoreConfig(self::XPATH_PLATFORM_EMAIL);
    }

    public function getAccountFirstname()
    {
        return Mage::getStoreConfig(self::XPATH_PLATFORM_FIRSTNAME);
    }

    public function getName()
    {
        return $this->getAccountFirstname() . ' ' . $this->getAccountLastname();
    }

    public function getAccountLastname()
    {
        return Mage::getStoreConfig(self::XPATH_PLATFORM_LASTNAME);
    }

    /**
     * Checks if current account has email support.
     * @return boolean True if account has email support, false otherwise.
     */
    public function hasEmailSupport()
    {
        $options = Mage::getStoreConfig(self::XPATH_PLATFORM_OPTIONS);
        $options = json_decode($options, true);

        if (is_array($options) && isset($options['email_support'])) {
            return $options['email_support'];
        }

        return false;
    }

}