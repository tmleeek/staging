<?php

/**
 * Rule Log Model. Each instance represents an execution log for a given rule.
 *
 * @method int getRuleId()
 * @method string getRuleName()
 * @method int getCustomerId()
 * @method string getExecutedDate()
 * @method TBT_Milestone_Model_Rule_Log setRuleId(int id)
 * @method TBT_Milestone_Model_Rule_Log setRuleName(string ruleName)
 * @method TBT_Milestone_Model_Rule_Log setCustomerId(string id)
 * @method TBT_Milestone_Model_Rule_Log setExecutedDate(string date)
 *
 */
class TBT_Milestone_Model_Rule_Log extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
       parent::_construct();
       $this->_init('tbtmilestone/rule_log');

       return $this;
    }

    /**
     * @param int $ruleId
     * @param int $customerId
     * @return int, The number of times a specific rule has been executed by a customer
     */
    public function getRuleExecutionCount($ruleId, $customerId)
    {
        $executionCount = $this->getCollection()
                               ->filterRuleLogsByCustomer($ruleId, $customerId)
                               ->getSize();

        return $executionCount;
    }

    /**
     * @param int $ruleId
     * @param int $customerId
     * @return boolean. True if any execution records were found for the specified rule and customer. False otherwise.
     */
    public function wasRuleEverExecuted($ruleId, $customerId)
    {
        return !($this->getRuleExecutionCount($ruleId, $customerId) == 0);
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        $milestoneDetails     = $this->getMilestoneDetails();
        $milestoneDetailsJson = json_encode($milestoneDetails);
        if (json_last_error() == JSON_ERROR_NONE) {
            // TODO: log error if one occurred
            $this->setMilestoneDetailsJson($milestoneDetailsJson);
        }

        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        $milestoneDetailsJson = $this->getMilestoneDetailsJson();
        $milestoneDetails = json_decode($milestoneDetailsJson, true);
        if ($milestoneDetails && is_array($milestoneDetails)) {
            $this->setMilestoneDetails($milestoneDetails);
        }

        return $this;
    }
}
