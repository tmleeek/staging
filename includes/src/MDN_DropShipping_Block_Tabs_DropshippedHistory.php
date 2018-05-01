<?php

class MDN_DropShipping_Block_Tabs_DropshippedHistory extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * 
     */
    public function __construct() {

        parent::__construct();
        
        $this->_parentTemplate = $this->getTemplate();

        $this->setId('DropshippedHistory'); // needed to use getGridUrl
        $this->setUseAjax(true);
        $this->setDefaultSort('dsposl_supplier_id', 'asc');
        
        $this->setRowClickCallback(false);
    }
    /**
     * @return type 
     */
    protected function _prepareCollection() {
       
        $collection = Mage::getModel('DropShipping/PurchaseOrderSupplierLog')
                            ->getCollection()
                            ->join('Purchase/Supplier', 'dsposl_supplier_id=sup_id')
                            ->join('Purchase/Order', 'dsposl_purchase_order_id=po_num')
                            ->join('sales/order', 'entity_id=dsposl_sales_order_id');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     *
     * @return type 
     */
    protected function _prepareColumns() {
/*
        $this->addColumn('dsposl_id', array(
            'header' => Mage::helper('DropShipping')->__('Log Id'),
            'index' => 'dsposl_id',
        ));
*/
        $this->addColumn('sup_name', array(
            'header' => Mage::helper('DropShipping')->__('Supplier'),
            'index' => 'sup_name',
            'renderer' => 'MDN_DropShipping_Block_Tools_Widget_Grid_Column_Renderer_Supplier',
        ));
    
        $this->addColumn('po_date', array(
            'header' => Mage::helper('DropShipping')->__('PO date'),
            'index' => 'po_date', 
            'type' => 'date'
        ));
        
        $this->addColumn('dsposl_purchase_order_id', array(
            'header' => Mage::helper('DropShipping')->__('Purchase Order #'),
            'index' => 'dsposl_purchase_order_id', 
            'renderer' => 'MDN_DropShipping_Block_Tools_Widget_Grid_Column_Renderer_PurchaseOrder',
        ));
        
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('DropShipping')->__('Sales Order #'),
            'index' => 'increment_id',
            'renderer' => 'MDN_DropShipping_Block_Tools_Widget_Grid_Column_Renderer_Order',
        ));
        
        $this->addColumn('products', array(
            'header' => Mage::helper('DropShipping')->__('Products'),
            'filter' => false,
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_History_Products',
        ));
        
        return parent::_prepareColumns();
    }

    /**
     * Url to refresh grid using ajax
     */
    public function getGridUrl() { 
        return $this->getUrl('DropShipping/Admin/DropshippedHistory', array('_current'=>true));
    }
    
    /**
     *
     * @return type 
     */
    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate , array('_relative' => true));
        return $this->fetchView($templateName);

    }

}
