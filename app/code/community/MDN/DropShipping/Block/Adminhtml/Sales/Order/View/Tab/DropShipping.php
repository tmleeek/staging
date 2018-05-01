<?php

class MDN_DropShipping_Block_Adminhtml_Sales_Order_View_Tab_DropShipping extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected function _construct() {
        parent::_construct();

        $this->_parentTemplate = $this->getTemplate();

        $this->setId('OrderDropShipItems'); // needed to use getGridUrl

        $this->setRowClickCallback(false);
    }

    /**
     * @return type 
     */
    protected function _prepareCollection() {

        $collection = Mage::getModel('sales/order_item')
                ->getCollection()
                ->addFieldToFilter('order_id', $this->getOrder()->getId())
                ->addFieldToFilter('purchase_order_id', array('gt' => 0));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     *
     * @return type 
     */
    protected function _prepareColumns() {

        $this->addColumn('item_id', array(
            'header' => Mage::helper('DropShipping')->__('#'),
            'index' => 'item_id'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('DropShipping')->__('Sku'),
            'index' => 'sku'
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('DropShipping')->__('Name'),
            'index' => 'name'
        ));
        
        $this->addColumn('qty_ordered', array(
            'header' => Mage::helper('DropShipping')->__('Qty'),
            'index' => 'qty_ordered'
        ));
        
        $this->addColumn('dropship_status', array(
            'header' => Mage::helper('DropShipping')->__('Dropship status'),
            'index' => 'dropship_status'
        ));
        
        $this->addColumn('purchase_order', array(
            'header' => Mage::helper('DropShipping')->__('PO'),
            'index' => 'purchase_order',
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_OrderItemPo'
        ));
        
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder() {
        return Mage::registry('current_order');
    }

    /**
     * 
     */
    public function getItems() {
        $items = array();

        foreach ($this->getOrder()->getAllItems() as $item) {
            $items[] = $item;
        }

        return $items;
    }

    /**
     * Retrieve source model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource() {
        return $this->getOrder();
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel() {
        return Mage::helper('DropShipping')->__('Drop Shipping');
    }

    public function getTabTitle() {
        return Mage::helper('DropShipping')->__('Drop Shipping');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

}