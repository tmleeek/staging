<?php

class TBT_Rewards_Model_Poll_Validator extends TBT_Rewards_Model_Special_Validator
{
    /**
     * Returns all rules that apply when voting poll
     * @return array(TBT_rewards_Model_Special)
     */
    public function getApplicableRulesOnPoll()
    {
        return $this->getApplicableRules( TBT_Rewards_Model_Poll_Poll::ACTION_CODE );
    }
}