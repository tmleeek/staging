<?php
/**
 * Twitter Helper
 */
class TBT_Rewardssocial_Helper_Twitter_Tweet extends Mage_Core_Helper_Abstract
{
    /**
     * This will load JS for Twitter Tweet rewarding only if needed
     *
     * @return file JS file to load in layout, or null
     */
    public function getJs()
    {
        if ($this->getRuleExists()) {
            return 'tbt/rewardssocial/twitter/tweet/reward.js';
        }

        return null;
    }

    /**
     * Will load CSS for Twitter Tweet button only if config option to show this
     * button is enabled.
     *
     * @return file CSS file to be loaded in layout, or null if option not enabled
     */
    public function getCss()
    {
        if (Mage::helper('rewardssocial/twitter_config')->isTweetingEnabled()) {
            return 'css/rewardssocial/twitter/tweet.css';
        }

        return null;
    }

    /**
     * Checks if any applicable rule exists for rewarding a user for tweeting on Twitter.
     * @return boolean
     */
    public function getRuleExists()
    {
        $rulesArray = Mage::getSingleton('rewardssocial/twitter_tweet_validator')->getApplicableRules();
        return !empty($rulesArray);
    }
}
