<?php
class TBT_Rewardssocial_Model_Pinterest_Pin_Special_Config extends TBT_Rewards_Model_Special_Configabstract
{
    const ACTION_CODE = 'social_pinterest_pin';

    public function _construct()
    {
        return parent::_construct();
        $this->setCaption(Mage::helper('rewardssocial')->__("Pinterest Pin"));
        $this->setDescription(Mage::helper('rewardssocial')->__("Customer will get points when they pin a page with Pinterest."));
        $this->setCode('social_pinterest_pin');
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
            self::ACTION_CODE => Mage::helper('rewardssocial')->__("Pins a page with Pinterest [BETA]")
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
