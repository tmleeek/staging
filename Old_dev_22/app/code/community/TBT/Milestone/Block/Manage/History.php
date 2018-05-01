<?php

class TBT_Milestone_Block_Manage_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'tbtmilestone';
        $this->_controller = 'manage_history';
        $this->_headerText = $this->__('Milestone History');

        return $this;
    }

    protected function _prepareLayout()
    {
        $this->removeButton('add');
        return parent::_prepareLayout();
    }
}
