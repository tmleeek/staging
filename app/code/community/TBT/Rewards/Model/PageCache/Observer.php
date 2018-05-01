<?php

class TBT_Rewards_Model_PageCache_Observer
{
    /**
     * Check if full page cache is enabled
     *
     * @return bool
     */
    public function isCacheEnabled()
    {
        return Mage::app()->useCache('full_page');
    }

    /**
     * Observes 'rewards_transfer_save_commit_after' and clear's header customer points balance FPC block cache, so
     * that it's refreshed on next load.
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function registerPointsChange($observer)
    {
        if (!$this->isCacheEnabled()) {
            return $this;
        }

        $event = $observer->getEvent();
        if (!$event) {
            return $this;
        }
        $transfer = $event->getRewardsTransfer();
        if (!$transfer || !$transfer->getCustomerId()) {
            return $this;
        }

        if (!$this->_getClearCache($transfer)) {
            return $this;
        }

        // we clear rewards header balance FPC cache by tag
        $cacheTag = md5(TBT_Rewards_Model_PageCache_Container_HeaderCustomerBalance::CACHE_TAG_PREFIX . $transfer->getCustomerId());
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($cacheTag));

        return $this;
    }

    /**
     * Checks if we need to clear block FPC cache based on transfer status. Customer points balance in header represents
     * usable points, so only transfers that have status Approved or Canceled should refresh block's cache
     * @param  TBT_Rewards_Model_Transfer $transfer
     * @return bool                       True if we need to clear points header block FPC cache, false otherwise
     */
    protected function _getClearCache($transfer)
    {
        $statuses = array(
            TBT_Rewards_Model_Transfer_Status::STATUS_APPROVED,
            TBT_Rewards_Model_Transfer_Status::STATUS_CANCELLED
        );
        if (in_array($transfer->getStatus(), $statuses) ||
            ($transfer->getStatus() == TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_EVENT
                && $transfer->getReasonId() == TBT_Rewards_Model_Transfer_Reason_Redemption::REASON_TYPE_ID)) {

            return true;
        }

        return false;
    }

}
