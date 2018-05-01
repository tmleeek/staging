<?php

class TBT_Milestone_Block_Manage_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'tbtmilestone';
        $this->_controller = 'manage_rule';

        $this->_headerText = $this->__("Milestones");
        $this->_addButtonLabel = $this->__("New Milestone");

        return $this;
    }
}
