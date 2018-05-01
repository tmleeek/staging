<?php
class TBT_Rewardssocial_Model_Facebook_Share_Special_Condition_Share extends TBT_Rewards_Model_Special_Condition_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->setCaption(Mage::helper('rewardssocial')->__("Facebook Product Share"));
        $this->setDescription(Mage::helper('rewardssocial')->__("Customer will get points when they share a product on Facebook."));
        $this->setCode(TBT_Rewardssocial_Model_Facebook_Share_Special_Config::ACTION_CODE);

        return $this;
    }

    public function givePoints(&$customer) { }

    public function revokePoints(&$customer) { }

    public function holdPoints(&$customer) { }

    public function cancelPoints(&$customer) { }

    public function approvePoints(&$customer) { }
}
