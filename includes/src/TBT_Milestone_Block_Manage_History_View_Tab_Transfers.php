<?php

class TBT_Milestone_Block_Manage_History_View_Tab_Transfers extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_collection = null;
    protected $_columnsSet = false;

    protected function _construct()
    {
        parent::_construct();

        $this->setId('milestoneRuleLogTransfers');
        $this->setDefaultSort('creation_ts');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        if (is_null($this->_collection)) {
            $ruleLog          = $this->_getCurrentLog();
            $milestoneDetails = $ruleLog->getMilestoneDetails();
            $referenceTypeId  = $milestoneDetails['condition']['reference_type_id'];

            $this->_collection = Mage::getModel('rewards/transfer')->getCollection()
                ->addFilter('reference_table.reference_id', $ruleLog->getRuleId())
                ->addFilter('reference_table.reference_type', $referenceTypeId)
                ->addFilter('customer_id', $ruleLog->getCustomerId())
                ->selectPointsCaption('points')
                ->addAllReferences();
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

        $this->addColumn('transfer_id', array(
            'header'       => Mage::helper('tbtmilestone')->__('ID'),
            'align'        => 'right',
            'width'        => '36px',
            'index'        => 'rewards_transfer_id',
            'filter_index' => 'main_table.rewards_transfer_id',
        ));

        $this->addColumn('points', array(
            'header'       => Mage::helper('tbtmilestone')->__('Points'),
            'align'        => 'left',
            'width'        => '70px',
            'index'        => 'points',
            'filter_index' => 'CONCAT(main_table.quantity, \' \', currency_table.caption)',
        ));

        $this->addColumn('comments', array(
            'header' => Mage::helper('tbtmilestone')->__('Comments/Notes'),
            'width'  => '250px',
            'index'  => 'comments'
        ));

        $statuses = Mage::getSingleton('rewards/transfer_status')->getOptionArray();
        $this->addColumn('status', array(
            'header'  => Mage::helper('tbtmilestone')->__('Status'),
            'align'   => 'left',
            'width'   => '80px',
            'index'   => 'status',
            'type'    => 'options',
            'options' => $statuses
        ));

        return parent::_prepareColumns();
    }

    protected function _getCurrentLog()
    {
        return Mage::registry('current_milestone_rule_log');
    }

    public function getTabLabel()
    {
        return $this->__("Points Transfers");
    }

    public function getTabTitle()
    {
        return $this->__("Points Transfers");
    }

    public function canShowTab()
    {
        if (!Mage::helper('tbtcommon')->getLoyaltyHelper('rewards')->isValid()) {
            return false;
        }

        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
