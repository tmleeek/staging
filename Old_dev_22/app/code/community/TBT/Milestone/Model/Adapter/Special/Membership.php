<?php

class TBT_Milestone_Model_Adapter_Special_Membership extends TBT_Milestone_Model_Adapter_Special_Abstract
{
    public function getConditionLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Reaches milestone for length of membership");
    }

    public function getFieldLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Number of days since signup");
    }

    public function getFieldComments()
    {
        return Mage::helper('tbtmilestone')->__("Magento's Cron must be functional for this rule.");
    }
}

?>