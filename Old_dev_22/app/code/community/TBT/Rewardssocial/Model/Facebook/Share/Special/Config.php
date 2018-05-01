<?php
class TBT_Rewardssocial_Model_Facebook_Share_Special_Config extends TBT_Rewards_Model_Special_Configabstract
{
    const ACTION_CODE = 'social_facebook_share';

    public function _construct()
    {
        return parent::_construct();
        $this->setCaption(Mage::helper('rewardssocial')->__("Facebook Product Share"));
        $this->setDescription(Mage::helper('rewardssocial')->__("Customer will get points when they share a product on Facebook."));
        $this->setCode(self::ACTION_CODE);
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
            self::ACTION_CODE => Mage::helper('rewardssocial')->__("Shares a product on Facebook")
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
