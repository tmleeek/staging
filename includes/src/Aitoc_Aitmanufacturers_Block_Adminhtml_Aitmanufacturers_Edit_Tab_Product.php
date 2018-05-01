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
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('aitmanufacturersGrid');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('product_sort');
        $this->setUseAjax(true); 
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    } 
    
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }   
    
    protected function _prepareCollection()
    {
		/* !AITOC_MARK:manufacturer_collection */
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->getRequest()->getParam('store'); 
        $manufacturer = Mage::getModel('aitmanufacturers/aitmanufacturers')->load($id);
        $attributeId = Mage::getModel('aitmanufacturers/config')->getAttributeId(Mage::app()->getRequest()->get('attributecode'));
        
        $productIds = $manufacturer->getProductsByManufacturer($manufacturer->getData('manufacturer_id'), $storeId, $attributeId);
        
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id') 
            ->addAttributeToSelect('sort')
            ->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1' . (
                    ((string)Mage::getConfig()->getModuleConfig('Aitoc_Aitquantitymanager')->active == 'true') ?
                    (' AND {{table}}.website_id='.Mage::App()->getStore($storeId)->getWebsite()->getId()) :
                    ''
                ),
                'left')
            ->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $storeId)
            ->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $storeId)
            ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $storeId)
            ->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $storeId)
            ->joinAttribute('sort', 'catalog_product/aitmanufacturers_sort', 'entity_id', null, 'left', $storeId) 
            ->addOrder('sort','ASC')
            ->addFieldToFilter('entity_id',array('in'=>$productIds));
        $this->setCollection($collection);
        Mage::register('collection', $collection); 
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('custom_name', array(
          'header'    => Mage::helper('aitmanufacturers')->__('Product'),
          'align'     =>'left',
          'index'     => 'custom_name',
          ));

        $this->addColumn('product_order', array(
          'index'     => 'sort',
          'header'    => Mage::helper('aitmanufacturers')->__('Sort Order'),
          'type'      => 'number',
          'align'     => 'center',
          'width'     => '100px',   
          'renderer'  => 'aitmanufacturers/adminhtml_aitmanufacturers_edit_tab_renderer_input',
        ));

        return parent::_prepareColumns();
    }
}