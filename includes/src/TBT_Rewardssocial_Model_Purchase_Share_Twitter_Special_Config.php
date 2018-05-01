<?php

class TBT_Rewardssocial_Model_Purchase_Share_Twitter_Special_Config extends TBT_Rewardssocial_Model_Purchase_Share_Abstract_Config
{
    const ACTION_CODE = 'social_purchase_share_twitter';

    public function getNewCustomerConditions()
    {
        return array(
            self::ACTION_CODE => Mage::helper('rewardssocial')->__('Shares a purchase on Twitter.'),
        );
    }
}
