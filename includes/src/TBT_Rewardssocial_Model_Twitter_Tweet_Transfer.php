<?php

class TBT_Rewardssocial_Model_Twitter_Tweet_Transfer extends TBT_Rewards_Model_Transfer
{

    public function _construct() {
        parent::_construct();
    }

    /**
     * Twitter Tweet in this context refers to a tuple
     * in the rewardssocial_twitter_tweet table which
     * contains information about the tweet action and
     * article (product, category, cms page, etc).
     *
     * @param unknown_type $id
     * @return unknown
     */
    public function setTwitterTweetId($id) {
        $this->clearReferences();
        $this->setReferenceType(TBT_Rewardssocial_Model_Twitter_Tweet_Reference::REFERENCE_TYPE_ID);
        $this->setReferenceId($id);
        $this->setReasonId(TBT_Rewards_Model_Transfer_Reason::REASON_CUSTOMER_DISTRIBUTION);
        $this->setData(TBT_Rewardssocial_Model_Twitter_Tweet_Reference::REFERENCE_KEY, $id);

        return $this;
    }

    public function isTwitterTweet() {
        return ($this->getReferenceType() == TBT_Rewardssocial_Model_Twitter_Tweet_Reference::REFERENCE_TYPE_ID) || isset($this->_data['newsletter_id']);
    }

    /**
     * Gets all transfers associated with the given twitter tweet ID
     *
     * @param int $twitter_tweet_id
     */
    public function getTransfersAssociatedWithTwitterTweet($twitter_tweet_id) {
        return $this->getCollection()->addFilter('reference_type', TBT_Rewardssocial_Model_Twitter_Tweet_Reference::REFERENCE_TYPE_ID)->addFilter('reference_id', $twitter_tweet_id);
    }

    /**
     * Fetches the transfer helper
     *
     * @return TBT_Rewards_Helper_Transfer
     */
    protected function _getTransferHelper() {
        return Mage::helper('rewards/transfer');
    }

    /**
     * Fetches the rewards special validator singleton
     *
     * @return TBT_Rewards_Model_Special_Validator
     */
    protected function _getSpecialValidator() {
        return Mage::getSingleton('rewards/special_validator');
    }

    /**
     * Fetches the rewards special validator singleton
     *
     * @return TBT_Rewards_Model_Special_Validator
     */
    protected function _getTwitterTweetValidator() {
        return Mage::getSingleton('rewardssocial/twitter_tweet_validator');
    }

    /**
     * Creates customer points transfers
     *
     * @param unknown_type $customer
     * @param unknown_type $tweet_id
     * @param unknown_type $rule
     * @return unknown
     */
    public function createTwitterTweetPoints($customerId, $tweet_id, $rule)
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
            $initial_status = Mage::getStoreConfig('rewards/InitialTransferStatus/AfterTwitterTweet', $store);
            if (!$transfer->setStatus(null, $initial_status)) {
                return false;
            }
        }

        // Translate the message through the core translation engine (nto the store view system) in case people want to use that instead
        // This is not normal, but we found that a lot of people preferred to use the standard translation system insteaed of the
        // store view system so this lets them use both.
        $initial_transfer_msg = Mage::getStoreConfig('rewards/transferComments/twitterTweet', $store);
        $comments = Mage::helper('rewardssocial')->__($initial_transfer_msg);

        $this->setTwitterTweetId($tweet_id)->setComments($comments)->setCustomerId($customerId)->save();

        return true;

    }

}
