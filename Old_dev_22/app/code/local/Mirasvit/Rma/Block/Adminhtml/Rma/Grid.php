<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rma_Block_Adminhtml_Rma_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
        $this->setDefaultSort('updated_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rma/rma')
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('increment_id', array(
            'header'    => Mage::helper('rma')->__('RMA #'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'increment_id',
            )
        );
        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('rma')->__('Order #'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'order_increment_id',
            )
        );
        $this->addColumn('name', array(
            'header'    => Mage::helper('rma')->__('Customer Name'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'name',
            )
        );
        $this->addColumn('status_id', array(
            'header'    => Mage::helper('rma')->__('Status'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'status_id',
            'type'      => 'options',
            'options'   => Mage::getModel('rma/status')->getCollection()->getOptionArray(),
            )
        );
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('rma')->__('Created Date'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'created_at',
            'type'      => 'datetime',
            )
        );
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('rma')->__('Updated Date'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'updated_at',
            'type'      => 'datetime',
            )
        );
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('rma_id');
        $this->getMassactionBlock()->setFormFieldName('rma_id');
        $statuses = array(
                array('label'=>'', 'value'=>''),
                array('label'=>$this->__('Disabled'), 'value'=> 0),
                array('label'=>$this->__('Enabled'), 'value'=> 1),
        );
        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('rma')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('rma')->__('Are you sure?')
        ));
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /************************/

}