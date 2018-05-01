<?php
 
class TBT_Milestone_Model_Adapter_Special_Referrals extends TBT_Milestone_Model_Adapter_Special_Abstract
{    
    public function getConditionLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Reaches milestone for number of referrals");
    }
    
    public function getFieldLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Number of Referrals");
    }
}

?>