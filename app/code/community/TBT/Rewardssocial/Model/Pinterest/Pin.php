<?php

class TBT_Rewardssocial_Model_Pinterest_Pin extends TBT_Rewardssocial_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('rewardssocial/pinterest_pin');
        return $this;
    }

    public function loadByCustomerAndUrl($customerId, $pinnedUrl)
    {
        $this->getResource()->loadByCustomerAndUrl($this, $customerId, $pinnedUrl);
        return $this;
    }

    public function hasAlreadyPinnedUrl($customerId, $url)
    {
        if (empty($customerId)) {
            throw new Exception("Must specify a Customer ID when checking if they have pinned the URL.");
        }
        if ($customerId instanceof Varien_Object) {
            $customerId = $customerId->getId();
        }

        $pin = Mage::getModel('rewardssocial/pinterest_pin');
        $pin->loadByCustomerAndUrl($customerId, $url);

        return (bool) $pin->getIsProcessed();
    }

    public function getTimeUntilNextPinAllowed($customer)
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

        $minimumWait = Mage::helper('rewardssocial/pinterest_config')->getMinSecondsBetweenPins($customer->getStore());
        $timeSinceLastLike = time() - $collection->getFirstItem()->getCreatedTs();
        $minimumWaitUntilNextPin = max(0, $minimumWait - $timeSinceLastLike);

        return $minimumWaitUntilNextPin;
    }

    public function isMaxDailyPinsReached($customer, $includeLastPin = true)
    {
        $maxPins = Mage::helper('rewardssocial/pinterest_config')->getMaxPinRewardsPerDay($customer->getStore());
        $time24HoursAgo = time() - (60 * 60 * 24);

        $allPinsInLastDay = $this->getCollection()
            ->addFilter('customer_id', $customer->getId())
            ->addFieldToFilter('UNIX_TIMESTAMP(created_time)', array('gteq' => $time24HoursAgo));

        $maxReached = ($includeLastPin)
            ? $allPinsInLastDay->getSize() >= $maxPins
            : $allPinsInLastDay->getSize() > $maxPins;

        return $maxReached;
    }
}
