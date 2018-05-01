<?php

class TBT_Rewardssocial_Model_Facebook_Like_Validator extends TBT_Rewards_Model_Special_Validator {

    /**
     *
     * @param TBT_Rewards_Model_Customer $customer
     * @return boolean
     */
    public function maxLikesReached($customer) {
        $max_likes = Mage::helper('rewardssocial/facebook_config')->getMaxLikeRewardsPerDay( $customer->getStore() );
        $current_time = time();
        $h24 = 60*60*24;
        $oldest_req_time = $current_time - $h24;

        $all_likes_since_time = Mage::getModel('rewardssocial/facebook_like')
            ->getCollection()
            ->addFilter('customer_id', $customer->getId())
            ->addFieldToFilter('UNIX_TIMESTAMP(created_time)', array('gteq' => $oldest_req_time));

        if($all_likes_since_time->count() >= $max_likes) {
            return true;
        }


        $like_transfers = Mage::getResourceModel('rewardssocial/facebook_like_transfer_collection')->filterCustomerRewardsSince($customer->getId(), $oldest_req_time);
        if($like_transfers->load()->count() >= $max_likes) {
            return true;
        }

        return false;
    }

    /**
     * Loops through each Special rule. If the rule applies and the customer didn't
     * already earn points for this like, then create (a) new points transfer(s) for the like.
     *
     * @return $this
     */
    public function initReward($facebook_account_id, $liked_url, $customer)
    {

        try {
            $ruleCollection = $this->getApplicableRules();

            if ($this->_likeExists($customer, $liked_url)) {
                throw new Exception(Mage::helper('rewardssocial')->__("You've already Liked this page."), 110);
            }

            $wait_time = Mage::getModel('rewardssocial/facebook_like')->getCollection()->getTimeUntilNextLikeAllowed($customer);
            if($wait_time > 0) {
                $waitTimeString = Mage::getModel('rewardssocial/facebook_like')->getFBWaitingTimeString($wait_time);
                throw new Exception(Mage::helper('rewardssocial')->__("Please wait %s before liking another page if you want to be rewarded.", $waitTimeString), 120);
            }


            $max_likes = Mage::helper('rewardssocial/facebook_config')->getMaxLikeRewardsPerDay($customer->getStore());
            if( $this->maxLikesReached($customer) ) {
                throw new Exception(Mage::helper('rewardssocial')->__("You've reached the Facebook like rewards limit for today (%s likes per day)", $max_likes), 130);
            }

            $like_model = Mage::getModel('rewardssocial/facebook_like')
                ->setCustomerId($customer->getId())
                ->setFacebookAccountId($facebook_account_id)
                ->setUrl($liked_url)
                ->save();

            if(! $like_model->getId()) {
                throw new Exception(Mage::helper('rewardssocial')->__("LIKE model was not saved for some reason. Customer ID {$customer->getId()}, LIKE url: {$liked_url}."), 10);
            }

            $this->_transferLikePoints($ruleCollection, $customer, $like_model);

        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }

        return $this;
    }


    /**
     * Cancels Facebook Like rewards.
     *
     * @return $this
     */
    public function cancelLikeRewards($facebook_account_id, $liked_url, $customer)
    {
        try {
            $ruleCollection = $this->getApplicableRules();

            // Only if a LIKE exists should we  cancel/revoke the LIKE and associated points.
            if (!$this->_likeExists($customer, $liked_url)) {
                return $this;
            }

            $like_model = Mage::getModel('rewardssocial/facebook_like')
                ->getCollection()
                ->addFilter('url', $liked_url)
                ->addFilter('customer_id', $customer->getId() )
                ->getFirstItem();

            // Cancel related points
            $this->_cancelUnlikedTransfers($like_model);

        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }

        return $this;
    }

    /**
     * Tries to cancel transfer associated with a Facebook Like model. If transfer is already approved,
     * it won't try to revoke it.
     *
     * @param TBT_Rewardssocial_Model_Facebook_Like $like_model
     * @return $this
     */
    protected function _cancelUnlikedTransfers($like_model)
    {
        $transfer_col = Mage::getResourceModel('rewardssocial/facebook_like_transfer_collection');
        $transfer_col->addFacebookLikeFilter($like_model);

        $cancellation_msg = Mage::helper('rewardssocial')->__("* Points cancelled because user unliked page on Facebook.");
        try {
            foreach($transfer_col as &$transfer) {
                $points_string = (string) Mage::getModel('rewards/points')->set( $transfer->getCurrencyId(), $transfer->getQuantity() );

                if ($transfer->getStatus() == TBT_Rewards_Model_Transfer_Status::STATUS_APPROVED) {
                    throw new Exception(Mage::helper('rewardssocial')->__('The <b>%s</b> you earned for liking this have have already been <b>approved</b> and can\'t be cancelled.', $points_string), 220);
                } else {
                    // Append the comments, then cancel.  Cancelling the transfer saves it too, so no need for a duplicate save.
                    $transfer->appendComments($cancellation_msg);
                    $transfer->cancel();

                    // Like the like model reference
                    // TODO Idealy we would add a deleted flag, but this is much quicker in the interest of time.
                    $like_model->delete();

                    throw new Exception(Mage::helper('rewardssocial')->__('The <b>%s</b> you earned for liking this have been <b>cancelled</b>.', $points_string), 210);
                }
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }

        return $this;
    }



    /**
     * @param TBT_Rewards_Model_Customer
     * @param string $liked_url
     * @return boolean
     */
    public function hasLikedPage($customer, $liked_url) {
        return $this->_likeExists($customer, $liked_url);
    }


    /**
     * @param TBT_Rewards_Model_Customer
     * @param string $liked_url
     * @return boolean
     */
    protected function _likeExists($customer, $liked_url) {
        $duplicate_like = Mage::getModel('rewardssocial/facebook_like')
            ->getCollection()->containsEntry($customer->getId(), $liked_url);
        return $duplicate_like;
    }



    /**
     * Goes through an already validated rule collection and transfers rule points to the customer specified
     * with the like model as the reference.
     * @param array(TBT_Rewards_Model_Special) $ruleCollection
     * @param TBT_Rewards_Model_Customer $customer
     * @param TBT_Rewardssocial_Model_Facebook_Like $like_model
     * @note: Adds messages to the session TODO: return messages instead of adding session messages
     */
    protected function _transferLikePoints($ruleCollection, $customer, $like_model) {
        foreach ($ruleCollection as $rule) {
            if (!$rule->getId()) {
                continue;
            }

            $transfer = Mage::getModel('rewardssocial/facebook_like_transfer');
            $is_transfer_successful = $transfer->createFacebookLikePoints(
                $customer,
                $like_model->getId(),
                $rule
            );

            if (!$is_transfer_successful) {
                throw new Exception("Failed to reward for Liking.");
            }
        }

        return $this;
    }

    /**
     * Returns all rules that apply when a customer likes something on Facebook.
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRules($action = null, $orAction = null)
    {
        if ($action === null) {
            $action = TBT_Rewardssocial_Model_Facebook_Like_Special_Config::ACTION_CODE;
        }

        return parent::getApplicableRules($action, $orAction);
    }

    /**
     * Returns all rules that apply wehn a customer likes something on facebook
     * @deprecated  see getApplicableRules()
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRulesOnFacebookLike() {
        return $this->getApplicableRules(TBT_Rewardssocial_Model_Facebook_Like_Special_Config::ACTION_CODE);
    }


    /**
     * Returns an array outlining the number of points they will receive for liking the item
     *
     * @return array
     */
    public function getPredictedFacebookLikePoints($page=null) {

        Varien_Profiler::start("TBT_Rewardssocial:: Predict Facebook Like Points");
        $ruleCollection = $this->getApplicableRules();

        $predict_array = array();
        foreach ($ruleCollection as $rule) {
            // TODO: shoud this be += ? I think so.
            // ksteffen: I think so, too. Changed code to do so.
            $currencyId = $rule->getPointsCurrencyId();
            if (!isset($predict_array[$currencyId])) {
                $predict_array[$currencyId] = 0;
            }
            $predict_array[$rule->getPointsCurrencyId()] += $rule->getPointsAmount();
        }

        Varien_Profiler::stop("TBT_Rewardssocial:: Predict Facebook Like Points");
        return $predict_array;
    }

}
