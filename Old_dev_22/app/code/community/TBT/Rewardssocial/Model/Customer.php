<?php

class TBT_Rewardssocial_Model_Customer extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('rewardssocial/customer');

        return $this;
    }

    public function getTimeUntilNextReferralShareAllowed()
    {
        $collection = Mage::getResourceModel('rewardssocial/referral_share_collection');
        $collection->filterAllSinceMinTime($this);
        $collection->getSelect()->columns(new Zend_Db_Expr("UNIX_TIMESTAMP(`created_time`) as `created_ts`"));
        $collection->setOrder('created_ts', 'DESC');
        $collection->getSelect()->limit(1);
        $collection->load();

        if ($collection->count() <= 0) {
            return 0;
        }

        $minimumWait = Mage::helper('rewardssocial/referral_config')->getMinSecondsBetweenShares($this->getStore());
        $timeSinceLastLike = time() - $collection->getFirstItem()->getCreatedTs();
        $minimumWaitUntilNextTweet = max(0, $minimumWait - $timeSinceLastLike);

        return $minimumWaitUntilNextTweet;
    }

    public function isMaxDailyReferralShareReached()
    {
        $maxTweets = Mage::helper('rewardssocial/referral_config')->getMaxReferralSharesPerDay($this->getStore());
        $time24HoursAgo = time() - (60 * 60 * 24);

        $allSharesInLastDay = Mage::getResourceModel('rewardssocial/referral_share_collection')
            ->addFilter('customer_id', $this->getId())
            ->addFieldToFilter('UNIX_TIMESTAMP(created_time)', array('gteq' => $time24HoursAgo));

        if ($allSharesInLastDay->getSize() >= $maxTweets) {
            return true;
        }

        return false;
    }
}
