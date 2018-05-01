<?php

class TBT_Rewardssocial_Helper_Purchase_Config extends Mage_Core_Helper_Abstract
{
    /**
     * Checks if Share Purchase Feature is enabled in Sweet Tooth configuration.
     * @return boolean Returns true if feature is enabled, false otherwise.
     */
    public function isSharePurchaseFeatureEnabled()
    {
        return Mage::getStoreConfig('rewards/purchase_share/enable');
    }
}