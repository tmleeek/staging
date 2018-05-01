<?php

class TBT_Milestone_Model_Resource_Rule_Log extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tbtmilestone/rule_log', 'log_id');
        return $this;
    }
}
