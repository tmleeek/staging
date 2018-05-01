<?php

class TBT_Rewardssocial_Helper_Pinterest_Pin extends Mage_Core_Helper_Abstract
{
    /**
     * This will load JS for Pinterest Pin rewarding only if needed
     *
     * @return file JS file to load in layout, or null
     */
    public function getJs()
    {
        if ($this->getRuleExists()) {
            return 'tbt/rewardssocial/pinterest/pin/reward.js';
        }

        return null;
    }

    /**
     * Will load CSS for Pinterest Pin button only if config option to show this
     * button is enabled.
     *
     * @return file CSS file to be loaded in layout, or null if option not enabled
     */
    public function getCss()
    {
        if (Mage::helper('rewardssocial/pinterest_config')->isPinningEnabled()) {
            return 'css/rewardssocial/pinterest/modal.css';
        }

        return null;
    }

    /**
     * Checks if rewarding for pinning is enabled: button enabled in config and rewarding rule exists.
     * @return boolean
     */
    public function isPinRewardingEnabled()
    {
        return Mage::helper('rewardssocial/pinterest_config')->isPinningEnabled() && $this->getRuleExists();
    }

    /**
     * Checks if any applicable rule exists for rewarding a user for pinning a product on Pinterest.
     * @return boolean
     */
    public function getRuleExists()
    {
        $rulesArray = Mage::getSingleton('rewardssocial/pinterest_pin_validator')->getApplicableRules();
        return !empty($rulesArray);
    }
}