<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_AdvancedStock_Block_Warehouse_Edit_Tabs_Products extends Mage_Adminhtml_Block_Widget_Grid
{
	private $_warehouseId = null;
	
    public function __construct()
    {
        parent::__construct();
        $this->setId('WarehouseProducts');
        $this->setUseAjax(true);
        $this->setEmptyText($this->__('No items'));
    }

    protected function _prepareCollection()
    {		            
    	$warehouse = mage::getModel('AdvancedStock/Warehouse')->load($this->getWarehouseId());
		$collection = $warehouse->getStocks();
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
   /**
     * Defini les colonnes du grid
     *
     * @return unknown
     */
    protected function _prepareColumns()
    {

            
        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('sku'),
            'index'     => 'sku'
        ));
                 
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));
               
        $this->addColumn('stock_qty', array(
            'header'    => Mage::helper('catalog')->__('Qty'),
            'index'     => 'stock_qty',
            'type' => 'number'
        ));
        
        $this->addColumn('stock_location', array(
            'header'    => Mage::helper('AdvancedStock')->__('Shelf<br>Location'),
            'index'     => 'stock_location'
        ));
        
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/ProductsGrid', array('_current'=>true, 'warehouse_id' => $this->getWarehouseId()));
    }

    
    public function getRowUrl($row)
    {
    	return $this->getUrl('AdvancedStock/Products/Edit', array('product_id' => $row->getentity_id()));
    }
    
    public function getWarehouseId()
    {
    	return $this->_warehouseId;
    }
    public function setWarehouseId($warehouseId)
    {
    	$this->_warehouseId = $warehouseId;
    	return $this;
    }
}
