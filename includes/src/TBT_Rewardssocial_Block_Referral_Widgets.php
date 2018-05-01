<?php

class TBT_Rewardssocial_Block_Referral_Widgets extends TBT_Rewardssocial_Block_Widgets
{
    protected $_widgetName = 'rewardsSocialReferralWidgetHover';
    protected $_widgetClass = 'rewardssocial-referral-widgets';
    protected $_widgetNotificationClass = 'rewardssocial-referral-widgets-points-notification';

    protected function _toHtml()
    {
        if (!Mage::helper('rewardssocial/referral_config')->getShowSocialShareButtons()) {
            return '';
        }

        return parent::_toHtml();
    }
}
