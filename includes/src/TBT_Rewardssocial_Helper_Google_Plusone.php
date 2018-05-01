<?php

class TBT_Rewardssocial_Helper_Google_Plusone extends Mage_Core_Helper_Abstract
{
    /**
     * This will load JS for google plusone rewarding only if needed
     *
     * @return file JS file to load in layout, or null
     */
    public function getJs()
    {
        if ($this->getRuleExists()) {
            return 'tbt/rewardssocial/google/plusone/reward.js';
        }

        return null;
    }

    /**
     * Checks if any applicable rule exists for rewarding a user for +1'ing on Google+.
     * @return boolean
     */
    public function getRuleExists()
    {
        $rulesArray = Mage::getSingleton('rewardssocial/google_plusOne_validator')->getApplicableRules();
        return !empty($rulesArray);
    }
}