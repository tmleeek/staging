<?php

class TBT_Rewardssocial_Block_Twitter_Tweet_Purchase_Button extends TBT_Rewardssocial_Block_Purchase_Share_Abstract
    implements TBT_Rewardssocial_Block_Twitter_Tweet_Button_Interface
{
    protected $_buttonType = 'Twitter';

    public function isCounterEnabled()
    {
        $countEnabled = Mage::helper('rewardssocial/twitter_config')->isTweetCountEnabled();
        return $countEnabled;
    }

    public function getTweetedUrl()
    {
        return $this->getProductUrl();
    }

    public function getTweet()
    {
        return Mage::helper('rewardssocial/twitter_config')->getTweetedMessage();
    }

}
