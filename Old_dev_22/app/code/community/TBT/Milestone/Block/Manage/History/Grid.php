<?php

class TBT_Milestone_Block_Manage_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_collection = null;
    protected $_columnsSet = false;

    protected function _construct()
    {
        parent::_construct();

        $this->setId('milestoneHistory');
        $this->setDefaultSort('executed_date');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        if (is_null($this->_collection)) {
            $this->_collection = Mage::getModel('tbtmilestone/rule_log')->getCollection()
            ->addCustomerNameToSelect();
        }

        $this->setCollection($this->_collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        if ($this->_columnsSet) {
            return parent::_prepareColumns();
        }

        $this->_columnsSet = true;

        $this->addColumn('log_id', array(
            'header' => $this->__("ID"),
            'index'  => 'log_id',
            'width'  => '36px',
            'align'  => 'left',
            'filter' => 'adminhtml/widget_grid_column_filter_range',
            'type'   => 'number',
        ));

        $this->addColumn('customer_name', array(
            'header'   => $this->__("Customer"),
            'index'    => "CONCAT(customer_firstname_table.value,' ',customer_lastname_table.value)",
            'renderer' => 'tbtmilestone/manage_grid_renderer_customer',
        ));

        $this->addColumn('rule_name', array(
            'header' => $this->__("Milestone Rule Applied"),
            'filter' => false,
            'index'  => 'rule_name',
        ));

        $this->addColumn('condition_satisfied', array(
            'header'   => $this->__("Condition Satisfied"),
            'index'    => 'milestone_details',
            'filter'   => false,
            'renderer' => 'tbtmilestone/manage_grid_renderer_milestone_condition',
        ));

        $this->addColumn('action_type', array(
            'header'   => $this->__("Action Type"),
            'index'    => 'action_type',
            'type'     => 'options',
            'options'  => $this->_getActionTypeOptions(),
            'renderer' => 'tbtmilestone/manage_grid_renderer_actionType',
        ));

        $this->addColumn('action_executed', array(
            'header'   => $this->__("Action Executed"),
            'index'    => 'milestone_details',
            'filter'   => false,
            'renderer' => 'tbtmilestone/manage_grid_renderer_milestone_action',
        ));

        $this->addColumn('executed_date', array(
            'header' => $this->__("Created At"),
            'index'  => 'executed_date',
            'width'  => '200px',
            'type'   => 'datetime',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

    protected function _getActionTypeOptions()
    {
        $options = Mage::getSingleton('tbtmilestone/rule_action_factory')->getTypeNames();
        return $options;
    }

}
