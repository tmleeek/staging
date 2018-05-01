<?php

class TBT_Milestone_Block_Manage_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();

        $this->setId('milestoneRules');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tbtmilestone/rule_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header' => $this->__("ID"),
            'index'  => 'rule_id',
            'width'  => '36px',
            'align'  => 'right'
        ));

        $this->addColumn('name', array(
            'header' => $this->__("Milestone Name"),
            'index'  => 'name'
        ));

        $this->addColumn('is_enabled', array(
            'header'  => $this->__("Status"),
            'index'   => 'is_enabled',
            'type'    => 'options',
            'width'   => '80px',
            'align'   => 'left',
            'options' => array(
                '1' => $this->__("Enabled"),
                '0' => $this->__("Disabled")
            )
        ));

        return parent::_prepareColumns();
    }

    /* TODO: hopefully add massaction stuff later
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField ( 'rewards_transfer_id' );
        $this->getMassactionBlock ()->setFormFieldName ( 'transfers' );

        $this->getMassactionBlock ()->addItem ( 'delete', array ('label' => Mage::helper ( 'rewards' )->__ ( 'Delete' ), 'url' => $this->getUrl ( '~/~/massDelete' ), 'confirm' => Mage::helper ( 'rewards' )->__ ( 'Are you sure?' ) ) );

        $statuses = Mage::getSingleton ( 'rewards/transfer_status' )->genSelectableStatuses ();

        $this->getMassactionBlock ()->addItem ( 'status', array ('label' => Mage::helper ( 'rewards' )->__ ( 'Change status' ), 'url' => $this->getUrl ( '~/~/massStatus', array ('_current' => true ) ), 'additional' => array ('visibility' => array ('name' => 'status', 'type' => 'select', 'class' => 'required-entry', 'label' => Mage::helper ( 'rewards' )->__ ( 'Status' ), 'values' => $statuses ) ) ) );

        return $this;
    }*/

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
