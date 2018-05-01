<?php

class TBT_Rewardssocial_Model_Twitter_Follow_Transfer extends TBT_Rewards_Model_Transfer
{
    public function isTwitterFollow()
    {
        return ($this->getReasonId() == TBT_Rewardssocial_Model_Twitter_Follow_Reason::REASON_TYPE_ID);
    }

    public function getTransfersAssociatedWithTwitterFollow()
    {
        return $this->getCollection()->addFilter('reason_id', TBT_Rewardssocial_Model_Twitter_Follow_Reason::REASON_TYPE_ID);
    }

    /**
     * Fetches the transfer helper
     *
     * @return TBT_Rewards_Helper_Transfer
     */
    protected function _getTransferHelper()
    {
        return Mage::helper('rewards/transfer');
    }

    /**
     * Fetches the rewards special validator singleton
     *
     * @return TBT_Rewards_Model_Special_Validator
     */
    protected function _getSpecialValidator()
    {
        return Mage::getSingleton('rewards/special_validator');
    }

    /**
     * Fetches the rewards special validator singleton
     *
     * @return TBT_Rewards_Model_Special_Validator
     */
    protected function _getTwitterFollowValidator()
    {
        return Mage::getSingleton('rewardssocial/twitter_follow_validator');
    }

    /**
     * Creates customer points transfers
     *
     * @param unknown_type $customer
     * @param unknown_type $tweet_id
     * @param unknown_type $rule
     * @return unknown
     */
    public function create($customerId, $rule)
    {
        $num_points = $rule->getPointsAmount();
        $currency_id = $rule->getPointsCurrencyId();
        $rule_id = $rule->getId();
        $transfer = $this->initTransfer($num_points, $currency_id, $rule_id);
        $store = Mage::app()->getStore();

        if (!$transfer) {
            return false;
        }

        //get On-Hold initial status override
        if ($rule->getOnholdDuration() > 0) {
            $transfer->setEffectiveStart(date('Y-m-d H:i:s', strtotime("+{$rule->getOnholdDuration()} days")))
                ->setStatus(null, TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_TIME);
        } else {
            //get the default starting status
            $initial_status = Mage::getStoreConfig('rewards/InitialTransferStatus/AfterTwitterFollow', $store);
            if (!$transfer->setStatus(null, $initial_status)) {
                return false;
            }
        }

        // Translate the message through the core translation engine (nto the store view system) in case people want to use that instead
        // This is not normal, but we found that a lot of people preferred to use the standard translation system insteaed of the
        // store view system so this lets them use both.
        $initial_transfer_msg = Mage::getStoreConfig('rewards/transferComments/twitterFollow', $store);
        $comments = Mage::helper('rewardssocial')->__($initial_transfer_msg);

        $this->setReasonId(TBT_Rewardssocial_Model_Twitter_Follow_Reason::REASON_TYPE_ID)
            ->setComments($comments)
            ->setCustomerId($customerId)
            ->save();

        return true;
    }
}
