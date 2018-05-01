<?php

class TBT_Rewardssocial_Model_Facebook_Share_Validator extends TBT_Rewards_Model_Special_Validator
{
    /**
     * Loops through Facebook product share customer behavioral rules and tries to reward customer for sharing this
     * product on Facebook. If for some reason the transfer creation fails we throw an exception which is handled in
     * the caller controller (TBT_Rewardssocial_IndexController::processFbProductShareAction()) .
     * @param  Mage_Customer_Model_Customer $customer  The customer for which we'll try rewarding points
     * @param  int $productShareId                     The shared product's ID
     * @return $this
     */
    public function initReward($customer, $productShareId)
    {
        try {
            $ruleCollection = $this->getApplicableRules();

            $this->_doTransfer($ruleCollection, $customer, $productShareId);

        } catch (Exception $e) {
            Mage::helper('rewards')->logException($e);
            throw new Exception(
                Mage::helper('rewardssocial')->__("Could not reward you for sharing a product on Facebook."),
                null, $e
            );
        }

        return $this;
    }

    protected function _doTransfer($ruleCollection, $customer, $productShareId)
    {
        foreach ($ruleCollection as $rule) {
            if (!$rule->getId()) {
                continue;
            }

            $transfer = Mage::getModel('rewardssocial/facebook_share_transfer');
            $isTransferSuccessful = $transfer->createFacebookProductSharePoints($customer, $productShareId, $rule);

            if (!$isTransferSuccessful) {
                throw new Exception("Failed to reward for sharing a product on Facebook.");
            }
        }

        return $this;
    }

    /**
     * Returns all rules that apply when a customer shares a product on Facebook.
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRules($action = null, $orAction = null)
    {
        if ($action === null) {
            $action = TBT_Rewardssocial_Model_Facebook_Share_Special_Config::ACTION_CODE;
        }

        return parent::getApplicableRules($action, $orAction);
    }

    /**
     * Returns all rules that apply when a customer shares a product on Facebook
     * @deprecated  see getApplicableRules()
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRulesOnFacebookShare()
    {
        return $this->getApplicableRules(TBT_Rewardssocial_Model_Facebook_Share_Special_Config::ACTION_CODE);
    }

    public function getPredictedPoints()
    {
        return $this->getPredictedFbSharePoints();
    }

    public function getPredictedFbSharePoints()
    {
        Varien_Profiler::start("TBT_Rewardssocial:: Predict Facebook Share Points");

        $ruleCollection = $this->getApplicableRules();
        $predictArray = array();
        foreach ($ruleCollection as $rule) {
            $currencyId = $rule->getPointsCurrencyId();
            if (!isset($predictArray[$currencyId])) {
                $predictArray[$currencyId] = 0;
            }
            $predictArray[$currencyId] += $rule->getPointsAmount();
        }

        Varien_Profiler::stop("TBT_Rewardssocial:: Predict Facebook Share Points");

        return $predictArray;
    }

    /**
     * Checks whether a customer has already earned points for sharing a specific product.
     * @param  int  $customerId The customer's ID.
     * @param  int  $productId  The product's ID.
     * @return boolean          True, if customer already earned points for sharing this product on Facebook.
     */
    public function hasAlreadySharedProduct($customerId, $productId)
    {
        return Mage::getModel('rewardssocial/facebook_share')->hasAlreadySharedProduct($customerId, $productId);
    }
}
