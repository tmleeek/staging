<?php

class TBT_Rewardssocial_Block_Twitter_Tweet_Referral_Button extends TBT_Rewardssocial_Block_Referral_Share_Button
    implements TBT_Rewardssocial_Block_Twitter_Tweet_Button_Interface
{
    public function isCounterEnabled()
    {
        return false;
    }

    public function getTweetedUrl()
    {
        return Mage::helper('rewardssocial/referral_share')->getReferralUrl($this->getCustomer());
    }

    public function getTweet()
    {
        return Mage::helper('rewardssocial/twitter_config')->getTweetedMessage();
    }
}
