<?php

class TBT_Rewardssocial_Model_Google_PlusOne_Special_Config extends TBT_Rewards_Model_Special_Configabstract
{
    const ACTION_CODE = 'social_google_plusOne';

    public function _construct()
    {
        return parent::_construct();
        $this->setCaption(Mage::helper('rewardssocial')->__("Google +1"));
        $this->setDescription(Mage::helper('rewardssocial')->__("Customer will get points when they +1 a page with Google+."));
        $this->setCode('social_google_plusOne');
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
            self::ACTION_CODE => Mage::helper('rewardssocial')->__("+1's a page with Google+")
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
