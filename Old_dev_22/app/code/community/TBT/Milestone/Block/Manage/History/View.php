<?php

class TBT_Milestone_Block_Manage_History_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'tbtmilestone';
        $this->_controller = 'manage_history';
        $this->_mode       = 'view';
        $this->_headerText = $this->__('Milestone History');

        return $this;
    }

    protected function _prepareLayout()
    {
        $this->removeButton('reset');
        $this->removeButton('save');
        $this->removeButton('delete');

        return parent::_prepareLayout();
    }
}
