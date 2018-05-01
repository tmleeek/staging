<?php

class TBT_Rewardssocial_Model_Mysql4_Twitter_Tweet extends TBT_Rewards_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rewardssocial/twitter_tweet', 'twitter_tweet_id');
    }

    public function loadByCustomerAndUrl(Mage_Core_Model_Abstract $object, $customerId, $url)
    {
        return $this->load($object, array($customerId, $url), array('customer_id', 'url'));
    }
}
