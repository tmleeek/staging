<?php

class TBT_Milestone_Model_Resource_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();

        $this->_init('tbtmilestone/rule');

        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        // We need to make sure the rule details are expanded.
        $this->walk('afterLoad');

        return $this;
    }
}
