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


class Mirasvit_Rma_Block_Adminhtml_Field_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid');
        $this->setDefaultSort('field_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rma/field')
            ->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $this->addColumn('field_id', array(
            'header'    => Mage::helper('rma')->__('ID'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'field_id',
            )
        );
        $this->addColumn('name', array(
            'header'    => Mage::helper('rma')->__('Title'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'name',
            )
        );
        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('rma')->__('Sort Order'),
//          'align'     => 'right',
//          'width'     => '50px',
            'index'     => 'sort_order',
            )
        );
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('field_id');
        $this->getMassactionBlock()->setFormFieldName('field_id');
        $statuses = array(
                array('label'=>'', 'value'=>''),
                array('label'=>$this->__('Disabled'), 'value'=> 0),
                array('label'=>$this->__('Enabled'), 'value'=> 1),
        );
        $this->getMassactionBlock()->addItem('is_active', array(
             'label'=> Mage::helper('rma')->__('Change status'),
             'url'  => $this->getUrl('*/*/massChange', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'is_active',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('rma')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
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