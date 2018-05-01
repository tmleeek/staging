<?php

class TBT_Rewardssocial_Helper_Referral_Share extends Mage_Core_Helper_Abstract
{
    /**
     * This will load JS for referral share rewarding only if needed, currently
     * if option is enabled in admin.
     * DO: should we check for rules
     *
     * @return file JS file to load in layout, or null
     */
    public function getJs()
    {
        if ($this->getRuleExists()) {
            return 'tbt/rewardssocial/referral/share/reward.js';
        }

        return null;
    }

    /**
     * Will load rewards CSS for 'Refer Friends' button only if config option to show this
     * button is enabled.
     *
     * @return file CSS file to be loaded in layout, or null if option not enabled
     */
    public function getRewardsCss()
    {
        if (Mage::helper('rewardssocial/referral_config')->isShareButtonEnabled()) {
            return 'css/rewardssocial/referral/share.css';
        }

        return null;
    }

    /**
     * Will load CSS for 'Refer Friends' button only if config option to show this
     * button is enabled.
     *
     * @return file CSS file to be loaded in layout, or null if option not enabled
     */
    public function getCss()
    {
        if (Mage::helper('rewardssocial/referral_config')->isShareButtonEnabled()) {
            return 'css/rewardsref/my_referrals.css';
        }

        return null;
    }

    /**
     * Checks if any applicable rule exists for rewarding a user for sharing their referral link.
     * @return boolean
     */
    public function getRuleExists()
    {
        $rulesArray = Mage::getModel('rewardssocial/referral_share_validator')->getApplicableRules();
        return !empty($rulesArray);
    }

    public function getReferralUrl($customer)
    {
        return (string)Mage::helper('rewardsref/url')->getUrl($customer);
    }

    /**
     * Turns out Facebook Send button will truncate URL to base domain if of type
     * <host>/rewardsref/index/refer/id/<user_id>/. Instead we can safely use something
     * like <host>/rewardsref/index/refer?id=<user_id>/
     *
     * @return string Referral URL formatted specially for Facebook Send button
     */
    public function getFbSendReferralUrl($customer)
    {
        $url = $this->getReferralUrl($customer);
        $url = str_replace('/id/', '?id=', $url);

        return $url;
    }
}