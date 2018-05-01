<?php
/**
 * Helper Data
 *
 * @category   TBT
 * @package    TBT_Rewardssocial
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewardssocial_Helper_Facebook_Config extends Mage_Core_Helper_Abstract
{

    public function getMaxLikeRewardsPerDay($store=null)
    {
        return (int) Mage::getStoreConfig('rewards/facebook/maxLikeRewardsPerDay', $store);
    }

    public function getMinSecondsBetweenLikes($store=null)
    {
        return (int) Mage::getStoreConfig('rewards/facebook/minSecondsBetweenLikes', $store);
    }

    /**
     * Checks if Facebook Like button is enabled in Sweet tooth configuration section.
     *
     * @return boolean True if button enabled, false otherwise
     */
    public function isLikingEnabled()
    {
        return Mage::getStoreConfigFlag('rewards/facebook/enableFacebookLike');
    }

    /**
     * Checks if Facebook product share button is available: the button is enabled and dependencies are met.
     * @return boolean True, if Facebook product share is ready for use, false otherwise.
     */
    public function isFbProductShareEnabled()
    {
        return $this->isFbProductShareButtonEnabled();
    }

    /**
     * Checks if Facebook Product Share button is enabled in Sweet tooth configuration section.
     * @return boolean True if button enabled, false otherwise
     */
    public function isFbProductShareButtonEnabled()
    {
        return Mage::getStoreConfigFlag('rewards/facebook/enableFacebookProductShare');
    }

    /**
     * Returns maximum number of Facebook products shares that will be rewarded per day, as configured in admin panel.
     * @param  Mage_Core_Model_Store $store
     * @return int Number of product shares that will be rewarded per day.
     */
    public function getMaxFacebookProductShareRewardsPerDay($store=null)
    {
        return (int) Mage::getStoreConfig('rewards/facebook/maxProductShareRewardsPerDay', $store);
    }

    /**
     * Returns mins seconds that need to pass before rewarding a new Facebook product share as configured in admin panel.
     * @param  Mage_Core_Model_Store $store Store
     * @return int  Number of seconds between 2 Facebook product shares
     */
    public function getMinSecondsBetweenFacebookProductShares($store=null)
    {
        return (int) Mage::getStoreConfig('rewards/facebook/minSecondsBetweenShares', $store);
    }

    /**
     * @deprecated unused
     */
    public function getAppId() {
        return '23dhfkjdhfkjsdfh4758479879237';//Mage::getStoreConfig('evlike_evlike_ev_facebook_app_id');
    }

    /**
     * @deprecated unused
     */
    public function getAppSecretId() {
        return '0cb3548d9f394bfashfsdoifhrobc251d3cf4622c2c29';
    }
}
