<?php

class TBT_Rewardssocial_Model_Purchase_Share_Abstract_Config extends TBT_Rewards_Model_Special_Configabstract
{
    public function _construct()
    {
        $this->setCaption("Purchase Share");
        $this->setDescription("Customer will be rewarded for sharing their purchase on social networks.");
        $this->setCode("social_purchase_share");

        return parent::_construct();
    }

    public function visitAdminConditions(&$fieldset)
    {
        return $this;
    }

    public function visitAdminActions(&$fieldset)
    {
        return $this;
    }

    public function getNewActions()
    {
        return array();
    }

    public function getAdminFormScripts()
    {
        return array();
    }

    public function getAdminFormInitScripts()
    {
        return array();
    }

    public function getNewCustomerConditions()
    {}
}
