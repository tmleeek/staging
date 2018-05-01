<?php
 
class TBT_Milestone_Model_Adapter_Special_Orders extends TBT_Milestone_Model_Adapter_Special_Abstract
{    
    public function getConditionLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Reaches milestone for number of orders");
    }
    
    public function getFieldLabel()
    {
        return  Mage::helper('tbtmilestone')->__("Number of Orders");
    }
}

?>