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
class MDN_AdvancedStock_Block_MassStockEditor_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('MassStockEditorGrid');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(Mage::helper('AdvancedStock')->__('No Items'));
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * 
     *
     * @return unknown
     */
    protected function _prepareCollection() {

        $collection = mage::getModel('cataloginventory/stock_item')
                ->getCollection()
                ->join('catalog/product', 'product_id=`catalog/product`.entity_id')
                ->addFieldToFilter('`catalog/product`.type_id', array('in' => array('simple', 'virtual')));

        //Product name
        $productNameAttributeId = mage::getModel('AdvancedStock/Constant')->GetProductNameAttributeId();
        $collection->join('AdvancedStock/CatalogProductVarchar', '`catalog/product`.entity_id=`AdvancedStock/CatalogProductVarchar`.entity_id and `AdvancedStock/CatalogProductVarchar`.store_id = 0 and `AdvancedStock/CatalogProductVarchar`.attribute_id = ' . $productNameAttributeId)
        ->addExpressionFieldToSelect('name', 'AdvancedStock/CatalogProductVarchar.value', $productNameAttributeId);

        
        //Manufacturer
        $manufacturerAttributeId = mage::getModel('AdvancedStock/Constant')->GetProductManufacturerAttributeId();
        $manufacturerCode = mage::getModel('AdvancedStock/Constant')->GetProductManufacturerAttributeCode();
        if($manufacturerCode){
           $collection->getSelect()->joinLeft(array('AdvancedStock/CatalogProductInt' => Mage::helper('HealthyERP')->getPrefixedTableName('catalog_product_entity_int')),
                        '`catalog/product`.entity_id = `AdvancedStock/CatalogProductInt`.entity_id and `AdvancedStock/CatalogProductInt`.store_id = 0 and `AdvancedStock/CatalogProductInt`.attribute_id = '.$manufacturerAttributeId,
                        array($manufacturerCode => 'value'));
        }
		
		//Gamme Collection
        $gammeAttributeId = mage::getModel('AdvancedStock/Constant')->GetProductGammeCollectionAttributeId();
      $gammeCode = mage::getModel('AdvancedStock/Constant')->GetProductGammeCollectionAttributeCode();
        if($gammeCode){
           $collection->getSelect()->joinLeft(array('AdvancedStock/CatalogProductInt_gamme' => Mage::helper('HealthyERP')->getPrefixedTableName('catalog_product_entity_int')),
                        '`catalog/product`.entity_id = `AdvancedStock/CatalogProductInt_gamme`.entity_id and `AdvancedStock/CatalogProductInt_gamme`.store_id = 0 and `AdvancedStock/CatalogProductInt_gamme`.attribute_id = '.$gammeAttributeId,
                        array($gammeCode => 'value'));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Columns grid definition
     *
     * @return unknown
     */
    protected function _prepareColumns() {
        $manufacturerCode = mage::getModel('AdvancedStock/Constant')->GetProductManufacturerAttributeCode();

        if($manufacturerCode){
            $this->addColumn('manufacturer', array(
                'header' => Mage::helper('AdvancedStock')->__('Manufacturer'),
                'index' => $manufacturerCode,
                'type' => 'options',
                'options' => mage::helper('AdvancedStock/Product_Base')->getManufacturerListForFilter(),
                'filter_index' => '`AdvancedStock/CatalogProductInt`.value'
            ));
        }
	   
            $this->addColumn('gamme_collection_new', array(
                'header' => Mage::helper('AdvancedStock')->__('Gamme Collection'),
                'index' => 'gamme_collection_new',
                'type' => 'options',
                'options' => mage::helper('AdvancedStock/Product_Base')->getGammeListForFilter(),
                'filter_index' => '`AdvancedStock/CatalogProductInt_gamme`.value'
            ));
        

        $this->addColumn('sku', array(
            'header' => Mage::helper('AdvancedStock')->__('Sku'),
            'index' => 'sku',
            'filter_index' => '`catalog/product`.sku'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('AdvancedStock')->__('Name'),
            'index' => 'name',
            'filter_index' => '`AdvancedStock/CatalogProductVarchar`.value'
        ));

        mage::helper('AdvancedStock/Product_ConfigurableAttributes')->addConfigurableAttributesColumn($this, 'product_id');


        $this->addColumn('stock_id', array(
            'header' => Mage::helper('AdvancedStock')->__('Warehouse'),
            'width' => '80',
            'index' => 'stock_id',
            'type' => 'options',
            'options' => Mage::getSingleton('AdvancedStock/System_Config_Source_Warehouse')->getListForFilter(),
        ));

        $this->addColumn('stock', array(
            'header' => Mage::helper('AdvancedStock')->__('Stock'),
            'index' => 'stock',
            'filter_index' => 'qty',
            'renderer' => 'MDN_AdvancedStock_Block_MassStockEditor_Widget_Grid_Column_Renderer_Stock',
            'align' => 'center',
            'type' => 'number'
        ));

        $this->addColumn('warning_stock_level', array(
            'header' => Mage::helper('AdvancedStock')->__('Warning stock level'),
            'filter' => false,
            'sortable' => false,
            'renderer' => 'MDN_AdvancedStock_Block_MassStockEditor_Widget_Grid_Column_Renderer_WarningStockLevel',
            'align' => 'center'
        ));

        $this->addColumn('ideal_stock_level', array(
            'header' => Mage::helper('AdvancedStock')->__('Ideal stock level'),
            'filter' => false,
            'sortable' => false,
            'renderer' => 'MDN_AdvancedStock_Block_MassStockEditor_Widget_Grid_Column_Renderer_IdealStockLevel',
            'align' => 'center'
        ));

        $this->addColumn('shelf_location', array(
            'header' => Mage::helper('AdvancedStock')->__('Location'),
            'index' => 'shelf_location',
            'renderer' => 'MDN_AdvancedStock_Block_MassStockEditor_Widget_Grid_Column_Renderer_StockLocation',
            'align' => 'center'
        ));
        
        //raise event to allow other modules to add columns
        Mage::dispatchEvent('advancedstock_masstockeditor_grid_preparecolumns', array('grid'=>$this));

        $this->addColumn('action', array(
            'header' => Mage::helper('AdvancedStock')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getproduct_id',
            'actions' => array(
                array(
                    'caption' => Mage::helper('AdvancedStock')->__('View'),
                    'url' => array('base' => 'AdvancedStock/Products/Edit'),
                    'field' => 'product_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        //raise event to allow other modules to add columns
        Mage::dispatchEvent('advancedstock_mass_stock_editor_grid_preparecolumns', array('grid' => $this));

        return parent::_prepareColumns();
    }

    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

    public function getGridUrl() {
        return $this->getUrl('AdvancedStock/Misc/MassStockEditorAjax', array('_current' => true));
    }

}
