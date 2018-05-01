<?php

class TBT_Rewardssocial_Helper_Google_Config extends Mage_Core_Helper_Abstract
{
    public function getMaxPlusOneRewardsPerDay($store = null)
    {
        return (int) Mage::getStoreConfig('rewards/google/maxPlusOneRewardsPerDay', $store);
    }

    public function getMinSecondsBetweenPlusOnes($store = null)
    {
        return (int) Mage::getStoreConfig('rewards/google/minSecondsBetweenPlusOnes', $store);
    }

    /**
     * Checks if Google +1 button is enabled in Sweet tooth configuration section.
     *
     * @return boolean True if button enabled, false otherwise
     */
    public function isGooglePlusEnabled()
    {
        return Mage::getStoreConfigFlag('rewards/google/enableGooglePlus');
    }

    /**
     * Checks if Google +1 counter is enabled in Sweet tooth configuration section.
     *
     * @return boolean True if button enabled, false otherwise
     */
    public function isGooglePlusCounterEnabled()
    {
        return Mage::getStoreConfigFlag('rewards/google/enableGooglePlusCount');
    }
}
