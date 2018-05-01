<?php

class TBT_Rewardssocial_Helper_Pinterest_Config extends Mage_Core_Helper_Abstract
{
    public function getMaxPinRewardsPerDay($store = null)
    {
        return (int) Mage::getStoreConfig('rewards/pinterest/maxPinRewardsPerDay', $store);
    }

    public function getMinSecondsBetweenPins($store = null)
    {
        return (int) Mage::getStoreConfig('rewards/pinterest/minSecondsBetweenPins', $store);
    }

    /**
     * Checks if Pinterest Pin button is enabled in Sweet tooth configuration section.
     *
     * @return boolean True if button enabled, false otherwise
     */
    public function isPinningEnabled()
    {
        return Mage::getStoreConfigFlag('rewards/pinterest/enablePinterestPin');
    }

    /**
     * Checks if Pinterest Pin count is enabled in Sweet tooth configuration section.
     *
     * @return boolean True if button enabled, false otherwise
     */
    public function isPinningCounterEnabled()
    {
        return Mage::getStoreConfigFlag('rewards/pinterest/enablePinterestPinCount');
    }
}
