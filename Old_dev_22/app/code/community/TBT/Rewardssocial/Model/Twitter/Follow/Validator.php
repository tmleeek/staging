<?php

class TBT_Rewardssocial_Model_Twitter_Follow_Validator extends TBT_Rewards_Model_Special_Validator
{
    /**
     * Loops through each Special rule. If the rule applies and the customer didn't
     * already earn points for this tweet, then create (a) new points transfer(s) for the tweet.
     */
    public function initReward($customerId, $twitterUserId = null)
    {
        try {
            $customer = Mage::getModel('rewardssocial/customer')->load($customerId);

            $ruleCollection = $this->getApplicableRules();
            $count = count($ruleCollection);

            $this->_transferFollowPoints($ruleCollection, $customerId, $customer);

        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
        }

        return $this;
    }

    /**
     * Goes through an already validated rule collection and transfers rule points to the customer specified
     * with the tweet model as the reference.
     * @param array(TBT_Rewards_Model_Special) $ruleCollection
     * @param TBT_Rewards_Model_Customer $customer
     * @param TBT_Rewardssocial_Model_Twitter_Tweet $tweetModel
     */
    protected function _transferFollowPoints($ruleCollection, $customerId, $customer)
    {
        foreach ($ruleCollection as $rule) {
            if (!$rule->getId()) {
                continue;
            }

            try {
                $transfer = Mage::getModel('rewardssocial/twitter_follow_transfer');
                $is_transfer_successful = $transfer->create($customerId, $rule);

                if (!$is_transfer_successful) {
                    throw new Exception(Mage::helper('rewardssocial')->__("Failed to reward points for following on Twitter."), 20);
                }

            } catch (Exception $ex) {
                throw new Exception($ex->getMessage(), $ex->getCode(), $ex);
            }
        }

        return $this;
    }

    /**
     * Returns all rules that apply when a customer Follows store on Twitter.
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRules($action = null, $orAction = null)
    {
        if ($action === null) {
            $action = TBT_Rewardssocial_Model_Twitter_Follow_Special_Config::ACTION_CODE;
        }

        return parent::getApplicableRules($action, $orAction);
    }

    /**
     * Returns all rules that apply wehn a customer tweets something on twitter
     * @deprecated  see getApplicableRules()
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRulesOnTwitterFollow()
    {
        return $this->getApplicableRules(
                TBT_Rewardssocial_Model_Twitter_Follow_Special_Config::ACTION_CODE
        );
    }

    /**
     * Returns an array outlining the number of points they will receive for liking the item
     *
     * @return array
     */
    public function getPredictedTwitterFollowPoints()
    {
        Varien_Profiler::start("TBT_Rewardssocial:: Predict Twitter Follow Points");
        $ruleCollection = $this->getApplicableRules();

        $predictArray = array();
        foreach ($ruleCollection as $rule) {
            $currencyId = $rule->getPointsCurrencyId();
            if (!isset($predictArray[$currencyId])) {
                $predictArray[$currencyId] = 0;
            }
            $predictArray[$currencyId] += $rule->getPointsAmount();
        }

        Varien_Profiler::stop("TBT_Rewardssocial:: Predict Twitter Follow Points");
        return $predictArray;
    }
}
