<?php

class TBT_Rewardssocial_Model_Purchase_Share_Facebook_Special_Config extends TBT_Rewardssocial_Model_Purchase_Share_Abstract_Config
{
    const ACTION_CODE = 'social_purchase_share_facebook';

    public function getNewCustomerConditions()
    {
        return array(
            self::ACTION_CODE => Mage::helper('rewardssocial')->__('Shares a purchase on Facebook.'),
        );
    }
}
