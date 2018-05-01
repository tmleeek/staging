<?php
class TBT_Rewardssocial_Model_Twitter_Follow_Special_Config extends TBT_Rewards_Model_Special_Configabstract
{
    const ACTION_CODE = 'social_twitter_follow';

    public function _construct()
    {
        return parent::_construct();
        $this->setCaption(Mage::helper('rewardssocial')->__("Twitter Follow"));
        $this->setDescription(Mage::helper('rewardssocial')->__("Customer will get points when they follow you Twitter."));
        $this->setCode('social_twitter_follow');
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
            self::ACTION_CODE => Mage::helper('rewardssocial')->__("Follows you on Twitter")
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
