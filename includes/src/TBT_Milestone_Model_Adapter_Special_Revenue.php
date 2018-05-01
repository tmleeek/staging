<?php

class TBT_Milestone_Model_Adapter_Special_Revenue extends TBT_Milestone_Model_Adapter_Special_Abstract
{
    public function getConditionLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Reaches milestone for revenue produced");
    }

    public function getFieldLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Revenue");
    }

    public function getFieldComments()
    {
        return "<strong>[" . (string) Mage::app()->getStore()->getBaseCurrencyCode() . "]</strong>";
    }
}
