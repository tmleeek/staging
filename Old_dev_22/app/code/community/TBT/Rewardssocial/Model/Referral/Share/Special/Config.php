<?php

class TBT_Rewardssocial_Model_Referral_Share_Special_Config extends TBT_Rewards_Model_Special_Configabstract
{
    const ACTION_CODE = 'social_referral_share';

    public function _construct()
    {
        return parent::_construct();
        $this->setCaption(Mage::helper('rewardssocial')->__("Referral Share"));
        $this->setDescription(Mage::helper('rewardssocial')->__("Customer will get points when they share their referral link."));
        $this->setCode('social_referral_link');
    }

    public function visitAdminActions(&$fieldset)
    {
        return $this;
    }

    public function visitAdminConditions(&$fieldset)
    {
        return $this;
    }

    public function getNewCustomerConditions()
    {
        return array(
            self::ACTION_CODE => Mage::helper('rewardssocial')->__("Shares their referral link")
        );
    }

    public function getNewActions()
    {
        return array ();
    }

    public function getAdminFormScripts()
    {
        return array ();
    }

    public function getAdminFormInitScripts()
    {
        return array ();
    }
}
