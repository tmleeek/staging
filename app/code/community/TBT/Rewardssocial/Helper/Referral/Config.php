<?php

class TBT_Rewardssocial_Helper_Referral_Config extends Mage_Core_Helper_Abstract
{
    public function getMaxReferralSharesPerDay($store = null)
    {
        return (int) Mage::getStoreConfig('rewards/referral/maxShareRewardsPerDay', $store);
    }

    public function getMinSecondsBetweenShares($store = null)
    {
        return (int) Mage::getStoreConfig('rewards/referral/minSecondsBetweenShares', $store);
    }

    /**
     * Checks whether configuration option to show the referral share button
     * (Referral Share Button On Frontend) is enabled or not.
     *
     * @return boolean
     */
    public function isShareButtonEnabled()
    {
        if (!Mage::helper('rewardssocial')->isModuleEnabled('TBT_RewardsReferral')) {
            return false;
        }

        return Mage::getStoreConfigFlag('rewards/referral/referral_share_button');
    }

    /**
     * Checks whether configuration option to show social share buttons is enabled
     *
     * @return boolean
     */
    public function getShowSocialShareButtons()
    {
        return Mage::getStoreConfigFlag('rewards/referral/show_social_share');
    }
}
