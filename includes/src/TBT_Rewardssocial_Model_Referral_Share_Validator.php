<?php

class TBT_Rewardssocial_Model_Referral_Share_Validator extends TBT_Rewards_Model_Special_Validator
{
    /**
     * Loops through each Special rule. If the rule applies and create (a) new
     * points transfer(s) for the referral link share.
     */
    public function initReward($customerId, $referralShareId)
    {
        try {
            $ruleCollection = $this->getApplicableRules();
            $count = count($ruleCollection);

            $this->_doTransfer($ruleCollection, $customerId, $referralShareId);

        } catch (Exception $e) {
            Mage::helper('rewards')->logException($e);
            throw new Exception(
                Mage::helper('rewardssocial')->__("Could not reward you for sharing your referral link."),
                null, $e
            );
        }

        return $this;
    }

    /**
     * Goes through an already validated rule collection and transfers rule points to the customer specified
     * with the $referralShareId as the reference.
     *
     * @param array(TBT_Rewards_Model_Special) $ruleCollection
     * @param TBT_Rewardssocial_Model_Customer $customer
     * @param int $referralShareId ID of the referral share
     */
    protected function _doTransfer($ruleCollection, $customerId, $referralShareId)
    {
        foreach ($ruleCollection as $rule) {
            if (!$rule->getId()) {
                continue;
            }

            $transfer = Mage::getModel('rewardssocial/referral_share_transfer');
            $is_transfer_successful = $transfer->create($customerId, $referralShareId, $rule);

            if (!$is_transfer_successful) {
                throw new Exception("Failed to reward for sharing referral link.");
            }
        }

        return $this;
    }

    /**
     * Returns all rules that apply when a customer shares their referral link
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRules($action = null, $orAction = null)
    {
        if ($action === null) {
            $action = TBT_Rewardssocial_Model_Referral_Share_Special_Config::ACTION_CODE;
        }

        return parent::getApplicableRules($action, $orAction);
    }

    /**
     * Returns an array outlining the number of points they will receive for sharing
     * their referral link
     *
     * @return array
     */
    public function getPredictedPoints()
    {
        Varien_Profiler::start("TBT_Rewardssocial:: Predict Referral Link Share Points");
        $ruleCollection = $this->getApplicableRules();

        $predictArray = array();
        foreach ($ruleCollection as $rule) {
            $currencyId = $rule->getPointsCurrencyId();
            if (!isset($predictArray[$currencyId])) {
                $predictArray[$currencyId] = 0;
            }
            $predictArray[$currencyId] += $rule->getPointsAmount();
        }

        Varien_Profiler::stop("TBT_Rewardssocial:: Predict Referral Link Share Points");

        return $predictArray;
    }
}
