<?php

class TBT_Rewardssocial_Model_Twitter_Tweet extends Mage_Core_Model_Abstract
{
    const TWITTER_SEARCH_URL = 'https://search.twitter.com/search.json';
    const TWITTER_SEARCH_OPTS = '&include_entities=1';
    // Gets an ID from /#st{id}/ where {id} is the customer's ID.
    const CUSTOMER_ID_EXTRACTION_REGEX = '/st(\d*)/';

    protected function _construct()
    {
        $this->_init('rewardssocial/twitter_tweet');
    }

    public function loadByCustomerId($customerId)
    {
        $this->load($customerId, 'customer_id');
        return $this;
    }

    public function loadByCustomerAndUrl($customerId, $url)
    {
        $this->getResource()->loadByCustomerAndUrl($this, $customerId, $url);
        return $this;
    }


    /* BEGIN SETTERS */

    /*public function setCustomerId($customerId)
    {
        $this->addData(array('customer_id' => $customerId));
    }

    public function setUrl($url)
    {
        $this->addData(array('url' => $url));
    }

    public function setTweetId($tweetId)
    {
        $this->addData(array('tweet_id' => $tweetId));
    }

    public function setTwitterUserId($twitterUserId)
    {
        $this->addData(array('twitter_user_id' => $twitterUserId));
    }*/

    /* END SETTERS */

    /**
     * Gets the latest (greatest) tweet ID in the database.
     * Tweet IDs are (mostly) chronologically ordered by Twitter,
     * i.e. id1 > id2 <==> id1 is newer than id2
     * ref: https://github.com/twitter/snowflake
     *
     * @return string ID
     */
    public function getLatestTweetId()
    {
        return $this->getCollection()
            ->getLatestTweetId();
    }

    public function hasAlreadyTweetedUrl($customer, $url)
    {
        $tweet = Mage::getModel('rewardssocial/twitter_tweet');
        $tweet->loadByCustomerAndUrl($customer->getId(), $url);
        return (bool) $tweet->getId();
    }

    public function getTimeUntilNextTweetAllowed($customer)
    {
        $collection = $this->getCollection();
        $collection->filterAllSinceMinTime($customer);
        $collection->getSelect()->columns(new Zend_Db_Expr("UNIX_TIMESTAMP(`created_time`) as `created_ts`"));
        $collection->setOrder('created_ts', 'DESC');
        $collection->getSelect()->limit(1);
        $collection->load();

        if ($collection->count() <= 0) {
            return 0;
        }

        $minimumWait = Mage::helper('rewardssocial/twitter_config')->getMinSecondsBetweenTweets($customer->getStore());
        $timeSinceLastLike = time() - $collection->getFirstItem()->getCreatedTs();
        $minimumWaitUntilNextTweet = max(0, $minimumWait - $timeSinceLastLike);

        return $minimumWaitUntilNextTweet;
    }

    public function isMaxDailyTweetsReached($customer)
    {
        $maxTweets = Mage::helper('rewardssocial/twitter_config')->getMaxTweetRewardsPerDay($customer->getStore());
        $time24HoursAgo = time() - (60 * 60 * 24);

        $allTweetsInLastDay = $this->getCollection()
            ->addFilter('customer_id', $customer->getId())
            ->addFieldToFilter('UNIX_TIMESTAMP(created_time)', array('gteq' => $time24HoursAgo));

        if ($allTweetsInLastDay->getSize() >= $maxTweets) {
            return true;
        }

        return false;
    }
}
