<?php

class TBT_Rewardssocial_Model_Google_PlusOne extends TBT_Rewardssocial_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('rewardssocial/google_plusOne');
        return $this;
    }

    public function loadByCustomerAndUrl($customerId, $url)
    {
        $this->getResource()->loadByCustomerAndUrl($this, $customerId, $url);
        return $this;
    }

    public function hasAlreadyPlusOnedUrl($customerId, $url)
    {
        if ($customerId instanceof Varien_Object) {
            $customerId = $customerId->getId();
        }

        $plusone = Mage::getModel('rewardssocial/google_plusOne');
        $plusone->loadByCustomerAndUrl($customerId, $url);
        return (bool) $plusone->getId();
    }

    public function getTimeUntilNextPlusOneAllowed($customer)
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

        $minimumWait = Mage::helper('rewardssocial/google_config')->getMinSecondsBetweenPlusOnes($customer->getStore());
        $timeSinceLastLike = time() - $collection->getFirstItem()->getCreatedTs();
        $minimumWaitUntilNextPin = max(0, $minimumWait - $timeSinceLastLike);

        return $minimumWaitUntilNextPin;
    }

    public function isMaxDailyPlusOnesReached($customer)
    {
        $maxPlusones = Mage::helper('rewardssocial/google_config')->getMaxPlusOneRewardsPerDay($customer->getStore());
        $time24HoursAgo = time() - (60 * 60 * 24);

        $allPlusonesInLastDay = $this->getCollection()
            ->addFilter('customer_id', $customer->getId())
            ->addFieldToFilter('UNIX_TIMESTAMP(created_time)', array('gteq' => $time24HoursAgo));

        if ($allPlusonesInLastDay->getSize() >= $maxPlusones) {
            return true;
        }

        return false;
    }
}
