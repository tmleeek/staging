<?php

class TBT_Rewardssocial_Block_Facebook_Share_Referral_Button extends TBT_Rewardssocial_Block_Referral_Share_Button
    implements TBT_Rewardssocial_Block_Facebook_Share_Button_Interface
{

    public function getOnClickAction()
    {
        $referralLink = Mage::helper('rewardssocial/referral_share')->getFbSendReferralUrl($this->getCustomer());
        $action = "fbShareAction(this, {url: '{$referralLink}', eventName: 'referral_share:response'}); return false;";

        return $action;
    }
}
