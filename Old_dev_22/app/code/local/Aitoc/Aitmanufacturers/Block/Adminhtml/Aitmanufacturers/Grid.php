<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('aitmanufacturersGrid');
        $this->setDefaultSort('manufacturer');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $attributeCode = $this->getRequest()->get('attributecode');
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
        $collection->addStoreFilter($this->getStoreId(), true);
        
        if (!$attributeCode)
        {
            /* dunno */
        } else {
            $attributeId = Mage::getModel('aitmanufacturers/config')->getAttributeId($attributeCode);
            $options = Mage::getModel('eav/entity_attribute_option')->getCollection()
                          ->addFieldToFilter('attribute_id', array('eq' => $attributeId))->toOptionArray();
            $ids = array();
            foreach ($options as $item)
            {
                $ids[] = $item['value'];
            }
            $collection->addFieldToFilter('main_table.manufacturer_id', array('in' => $ids));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
  
    protected function getStoreId()
    {
        return Mage::registry('store_id');
    }

    protected function _prepareColumns()
    {
        $attrName = Mage::getModel('aitmanufacturers/config')->getAttributeName($this->getRequest()->get('attributecode'));
        /*
        $this->addColumn('id', array(
            'header'    => Mage::helper('aitmanufacturers')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'id',
        ));
        */
        
        $this->addColumn('manufacturer', array(
            'header'    => Mage::helper('aitmanufacturers')->__($attrName),
            'align'     =>'left',
            'width'     => '200px',
            'index'     => 'manufacturer',
        ));
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('aitmanufacturers')->__($attrName.' Page Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));
        
        $this->addColumn('url_key', array(
            'header'    => Mage::helper('aitmanufacturers')->__('URL Key'),
            'align'     =>'left',
            'index'     => 'url_key',
        ));
        
        $layouts = array();
        foreach (Mage::getConfig()->getNode('global/aitmanufacturers/layouts')->children() as $layoutName=>$layoutConfig) 
        {
            $layouts[$layoutName] = (string)$layoutConfig->label;
        }
        
        $this->addColumn('root_template', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Layout'),
            'index'     => 'root_template',
            'type'      => 'options',
            'options'   => $layouts,
        ));
        
        /*
        if (!Mage::app()->isSingleStoreMode()) 
        {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('aitmanufacturers')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }
        */
        
        $this->addColumn('featured', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Featured'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'featured',
            'type'      => 'options',
            'options'   => array(
                0 => 'No',
                1 => 'Yes',
            ),
        ));
        
        $this->addColumn('status', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));
        
        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('aitmanufacturers')->__('Sort Order'),
            'align'     =>'left',
            'width'     => '30px',
            'index'     => 'sort_order',
        ));
        
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('aitmanufacturers')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('aitmanufacturers')->__('Edit'),
                        'url'       => array(
                            'base' => '*/*/edit',
                            'params' => array('store' => $this->getStoreId(), 'attributecode' => $this->getRequest()->get('attributecode')),
                        ),
                        'field'     => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
        
        //$this->addExportType('*/*/exportCsv', Mage::helper('aitmanufacturers')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('aitmanufacturers')->__('XML'));
        return parent::_prepareColumns();
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
    
    protected function _prepareMassaction()
    {
        $attributeCode = $this->getRequest()->get('attributecode');
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('aitmanufacturers');
        
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('aitmanufacturers')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete', array('store' => $this->getStoreId(), 'attributecode' => $attributeCode)),
             'confirm'  => Mage::helper('aitmanufacturers')->__('Are you sure?')
        ));
        
        $statuses = Mage::getSingleton('aitmanufacturers/status')->getOptionArray();
        
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('aitmanufacturers')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true, 'store' => $this->getStoreId(), 'attributecode' => $attributeCode)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('aitmanufacturers')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        
        return $this;
    }
    
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'store' => $this->getStoreId(), 'attributecode' => $this->getRequest()->get('attributecode')));
    }
    
}