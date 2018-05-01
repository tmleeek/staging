<?php

class TBT_Milestone_Model_Adapter_Special_Inactivity extends TBT_Milestone_Model_Adapter_Special_Abstract
{
    public function getConditionLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Reaches an inactivity period");
    }

    public function getFieldLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Number of Inactive Days");
    }

    public function getFieldComments()
    {
        return Mage::helper('tbtmilestone')->__("Magento's Cron must be functional for this rule.");
    }
}

?>