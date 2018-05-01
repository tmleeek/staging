<?php

class TBT_Rewardssocial_Model_Facebook_Share extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rewardssocial/facebook_share');
    }

    public function loadByCustomerId($customerId)
    {
        $this->load($customerId, 'customer_id');
        return $this;
    }

    public function loadByCustomerAndProductId($customerId, $productId)
    {
        $this->getResource()->loadByCustomerAndProductId($this, $customerId, $productId);
        return $this;
    }

    /**
     * Checks if a product has been already shared on Facebook by a customer.
     * @param  int  $customerId Customer ID
     * @param  int  $productId  Product ID
     * @return boolean          True if product has already been share by customer, false otherwise.
     */
    public function hasAlreadySharedProduct($customerId, $productId)
    {
        $share = Mage::getModel('rewardssocial/facebook_share')->loadByCustomerAndProductId($customerId, $productId);
        return (bool) $share->getId();
    }

    /**
     * Calculates the amount of time that still needs to pass until next Facebook product share will be rewarded from
     * this moment, based on configuration option that specifies the minimum amount of time between to share rewards.
     *
     * @param  Mage_Customer_Model_Customer $customer The customer for which we check this.
     * @return int Amount of time before a new Facebook product share will be rewarded.
     */
    public function getTimeUntilNextShareAllowed($customer)
    {
        $collection = $this->getCollection();
        $collection->filterAllSinceMinTime($customer);
        $collection->getSelect()->columns(new Zend_Db_Expr("UNIX_TIMESTAMP(`created_time`) as `created_ts`"));
        $collection->setOrder('created_ts', 'DESC');
        $collection->getSelect()->limit(1);

        if ($collection->getSize() <= 0) {
            return 0;
        }

        $minimumWait = Mage::helper('rewardssocial/facebook_config')->getMinSecondsBetweenFacebookProductShares($customer->getStore());
        $timeSinceLastShare = time() - $collection->getFirstItem()->getCreatedTs();
        $minimumWaitUntilNextShare = max(0, $minimumWait - $timeSinceLastShare);

        return $minimumWaitUntilNextShare;
    }

    /**
     * Checks whether maximum Facebook product share rewards has been reached for the day.
     * @param  Mage_Customer_Model_Customer $customer The customer for which we check this.
     * @return boolean           True, if maximum share rewards limit has been reached, false otherwise.
     */
    public function isMaxDailyProductSharesReached($customer)
    {
        $maxProductShares = Mage::helper('rewardssocial/facebook_config')->getMaxFacebookProductShareRewardsPerDay($customer->getStore());
        $time24HoursAgo = time() - (60 * 60 * 24);

        $allProductSharesInLastDay = $this->getCollection()
            ->addFieldToFilter('customer_id', array('eq' => (string)$customer->getId()))
            ->addFieldToFilter('UNIX_TIMESTAMP(created_time)', array('gteq' => $time24HoursAgo));

        if ($allProductSharesInLastDay->getSize() >= $maxProductShares) {
            return true;
        }

        return false;
    }
}