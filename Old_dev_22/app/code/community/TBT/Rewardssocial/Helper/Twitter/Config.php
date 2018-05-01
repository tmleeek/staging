<?php

class TBT_Rewardssocial_Helper_Twitter_Config extends Mage_Core_Helper_Abstract
{
    public function getMaxTweetRewardsPerDay($store = null)
    {
        return (int) Mage::getStoreConfig('rewards/twitter/maxTweetRewardsPerDay', $store);
    }

    public function getMinSecondsBetweenTweets($store = null)
    {
        return (int) Mage::getStoreConfig('rewards/twitter/minSecondsBetweenTweets', $store);
    }

    /**
     * Checks if Twitter Tweet button is enabled in Sweet tooth configuration section.
     *
     * @return boolean True if button enabled, false otherwise
     */
    public function isTweetingEnabled()
    {
        return Mage::getStoreConfigFlag('rewards/twitter/enableTwitterTweet');
    }

     /**
     * Checks if Twitter Tweet counts is enabled in Sweet tooth configuration section.
     *
     * @return boolean True if button enabled, false otherwise
     */
    public function isTweetCountEnabled()
    {
        return Mage::getStoreConfigFlag('rewards/twitter/enableTweetCount');
    }

    /**
     * Get default tweet message as configured in admin.
     * @return string
     */
    public function getTweetedMessage()
    {
        return Mage::getStoreConfig('rewards/twitter/tweetedMessage');
    }
}
