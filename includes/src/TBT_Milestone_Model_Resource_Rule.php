<?php

class TBT_Milestone_Model_Resource_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tbtmilestone/rule', 'rule_id');
        return $this;
    }
}
