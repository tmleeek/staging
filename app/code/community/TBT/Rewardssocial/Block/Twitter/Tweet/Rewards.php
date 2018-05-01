<?php

class TBT_Rewardssocial_Block_Twitter_Tweet_Rewards extends TBT_Rewardssocial_Block_Abstract
{

    public function _toHtml()
    {
        // this should check actually, if a rewarding rule exists not if the button is used
        if (Mage::helper('rewardssocial/twitter_config')->isTweetingEnabled()) {
            return parent::_toHtml();
        }

        return '';
    }

    public function getTweetProcessingUrl()
    {
        return $this->getUrl('rewardssocial/index/processTweets');
    }
}