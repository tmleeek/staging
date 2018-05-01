<?php

/**
 * Twitter Tweet Collection class
 */
class TBT_Rewardssocial_Model_Mysql4_Twitter_Tweet_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('rewardssocial/twitter_tweet');
    }

    /**
     * Returns the greatest (most recent) tweet ID in the table.
     * Used to retrieve only new tweets from the Twitter API.
     *
     * @return int tweet ID
     */
    public function getLatestTweetId()
    {
        // get the one tweet with the highest tweet_id
        $collection = $this;
        $collection->setOrder('tweet_id', 'DESC');
        $collection->getSelect()->limit(1);
        $collection->load();
        $latestTweetId = $collection->getFirstItem()->getData('tweet_id');
        return ($latestTweetId) ? $latestTweetId : 0;
    }

    public function containsEntry($customerId, $url)
    {
        $collection = $this;
        $collection->addFieldToFilter('customer_id', $customerId);
        $collection->addFieldToFilter('url', $url);
        $collection->load();
        return ($collection->getSize() > 0);
    }

    public function filterAllSinceMinTime($customer)
    {
        $minimumWait = Mage::helper('rewardssocial/twitter_config')->getMinSecondsBetweenTweets($customer->getStore());
        $now = time();
        $oldestRequiredTime = $now - $minimumWait;

        $this->addFilter('customer_id', $customer->getId())
            ->addFieldToFilter('UNIX_TIMESTAMP(`created_time`)', array('gteq' => $oldestRequiredTime));

        return $this;
    }
}
