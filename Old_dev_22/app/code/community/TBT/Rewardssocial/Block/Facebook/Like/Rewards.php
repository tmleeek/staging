<?php

class TBT_Rewardssocial_Block_Facebook_Like_Rewards extends TBT_Rewardssocial_Block_Abstract
{
    public function _toHtml()
    {
        // this should check actually, if a rewarding rule exists not if the button is used
        if (Mage::helper('rewardssocial/facebook_config')->isLikingEnabled()
            && Mage::helper('rewardssocial')->isModuleEnabled('Evolved_Like')) {
            return parent::_toHtml();
        }

        return '';

    }

    public function getRewardUrl()
    {
        //$params = array();
        $url = $this->getUrl('rewardssocial/facebook_like/onLike');

        return $url;
    }


}