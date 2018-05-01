<?php

class TBT_Rewardssocial_Helper_Facebook_Share extends Mage_Core_Helper_Abstract
{
    /**
     * This will load JS for facebook share rewarding only if needed, currently
     * if option is enabled in admin.
     * DO: should we check for rules
     *
     * @return file JS file to load in layout, or null
     */
    public function getJs()
    {
        if ($this->getRuleExists()) {
            return 'tbt/rewardssocial/facebook/share/reward.js';
        }

        return null;
    }

    /**
     * Checks if any applicable rule exists for rewarding a user for sharing a product on Facebook.
     * @return boolean
     */
    public function getRuleExists()
    {
        $rulesArray = Mage::getSingleton('rewardssocial/facebook_share_validator')->getApplicableRules();
        return !empty($rulesArray);
    }
}