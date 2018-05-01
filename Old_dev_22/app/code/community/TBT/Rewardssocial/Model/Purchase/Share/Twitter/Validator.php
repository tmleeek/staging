<?php

class TBT_Rewardssocial_Model_Purchase_Share_Twitter_Validator extends TBT_Rewardssocial_Model_Purchase_Share_Abstract_Validator
{

    /**
     * Returns all rules that apply when a customer shares a purchase on Twitter.
     * @return array(TBT_Rewards_Model_Special)
     */
    public function getApplicableRules($action = null, $orAction = null)
    {
        if ($action === null) {
            $action = TBT_Rewardssocial_Model_Purchase_Share_Twitter_Special_Config::ACTION_CODE;
        }

        return parent::getApplicableRules($action, $orAction);
    }

}
