<?php
/**
 * Twitter Helper
 */
class TBT_Rewardssocial_Helper_Twitter_Follow extends Mage_Core_Helper_Abstract
{
    /**
     * Checkes whether all configuration conditions for Twitter Follow are met.
     *
     * @return boolean true if Twitter username configured and button set to be shown, false otherwise
     */
    public function isFollowingEnabled()
    {
        $buttonEnabled = $this->isFollowButtonEnabled();
        $usernameSet = trim($this->getStoreTwitterUsername()) != '';

        return $buttonEnabled && $usernameSet;
    }

    /**
     * Returns true if option to show Twitter Follow button is enabled in
     * configuration section
     *
     * @return boolean
     */
    public function isFollowButtonEnabled()
    {
        return Mage::getStoreConfig('rewards/twitter/enableTwitterFollow');
    }

    /**
     * Checks if Twitter Follow count display is enabled in Sweet tooth configuration section.
     *
     * @return boolean True if button enabled, false otherwise
     */
    public function isFollowCountEnabled()
    {
        return Mage::getStoreConfigFlag('rewards/twitter/showCount');
    }

    /**
     * Returns the username of the store's Twitter account, as set in the Admin Panel.
     *
     * @return string
     */
    public function getStoreTwitterUsername()
    {
        return Mage::getStoreConfig('rewards/twitter/storeTwitterUsername');
    }

    /**
     * This will load JS for twitter follow rewarding only if needed
     *
     * @return file JS file to load in layout, or null
     */
    public function getJs()
    {
        if ($this->getRuleExists()) {
            return 'tbt/rewardssocial/twitter/follow/reward.js';
        }

        return null;
    }

    /**
     * Checks if any applicable rule exists for rewarding a user for following us on Twitter.
     * @return boolean
     */
    public function getRuleExists()
    {
        $rulesArray = Mage::getSingleton('rewardssocial/twitter_follow_validator')->getApplicableRules();
        return !empty($rulesArray);
    }

}
