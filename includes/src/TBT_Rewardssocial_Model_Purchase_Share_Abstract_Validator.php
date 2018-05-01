<?php

class TBT_Rewardssocial_Model_Purchase_Share_Abstract_Validator extends TBT_Rewards_Model_Special_Validator
{

    /**
     * Loops through each Special rule. If the rule applies and the customer didn't
     * already earn points for this pin, then create (a) new points transfer(s) for the pin.
     * @return $this
     */
    public function initReward($customer, $purchaseId, $actionType)
    {
        try {
            $ruleCollection = $this->getApplicableRules();
            $count = count($ruleCollection);

            $this->_doTransfer($ruleCollection, $customer, $purchaseId, $actionType);

        } catch (Exception $e) {
            Mage::helper('rewards')->logException($e);
            throw new Exception(
                Mage::helper('rewardssocial')->__("Could not reward you for sharing your purchase."),
                null, $e
            );
        }

        return $this;
    }

    /**
     * Goes through an already validated rule collection and transfers rule points to the customer specified
     * with the pin model as the reference.
     * @param array(TBT_Rewards_Model_Special) $ruleCollection
     * @param TBT_Rewards_Model_Customer $customer
     * @param TBT_Rewardssocial_Model_Pinterest_Pin $pinModel
     * @return $this
     */
    protected function _doTransfer($ruleCollection, $customer, $purchaseId, $actionType)
    {
        foreach ($ruleCollection as $rule) {
            if (!$rule->getId()) {
                continue;
            }

            $transfer = Mage::getModel('rewardssocial/purchase_share_transfer');
            $is_transfer_successful = $transfer->trigger(
                    $customer,
                    $purchaseId,
                    $rule,
                    $actionType
            );

            if (!$is_transfer_successful) {
                throw new Exception("Failed to reward for pin.");
            }
        }

        return $this;
    }

    /**
     * Returns an array outlining the number of points they will receive for sharing this purchased product on Twitter.
     *
     * @return array
     */
    public function getPredictedPoints()
    {

        Varien_Profiler::start("TBT_Rewardssocial:: Predict Purchase Sharing Points");
        $ruleCollection = $this->getApplicableRules();

        $predictArray = array();
        foreach ($ruleCollection as $rule) {
            $currencyId = $rule->getPointsCurrencyId();
            if (!isset($predictArray[$currencyId])) {
                $predictArray[$currencyId] = 0;
            }
            $predictArray[$currencyId] += $rule->getPointsAmount();
        }

        Varien_Profiler::stop("TBT_Rewardssocial:: Predict Purchase Sharing Points");

        return $predictArray;
    }
}
