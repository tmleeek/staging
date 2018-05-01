<?php

class MDN_DropShipping_Block_Tabs_PendingSupplierDelivery extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();
        $this->setId('dropShippPendingSupplierDelivery');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(__('No items'));
        $this->setSaveParametersInSession(true);
        $this->setRowClickCallback(false);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        
        $collection = Mage::getModel('Purchase/Order')->getCollection()->addFieldToFilter('po_status', MDN_Purchase_Model_Order::STATUS_WAITING_FOR_DELIVERY);
        $collection->join('Purchase/Supplier', 'po_sup_num=sup_id');
        $collection->addFieldToFilter('is_drop_ship', 1);

        $this->setCollection($collection);
        return parent::_prepareCollection();
        
    }

    protected function _prepareColumns() {

        //Organizer
        $this->addColumn('organizer', array(
            'header' => Mage::helper('Organizer')->__('Organizer'),
            'renderer' => 'MDN_Organizer_Block_Widget_Column_Renderer_Comments',
            'align' => 'center',
            'entity' => 'purchase_order',
            'filter' => false,
            'sort' => false
        ));


        $this->addColumn('po_date', array(
            'header' => Mage::helper('sales')->__('Created at'),
            'index' => 'po_date',
            'type' => 'datetime',
            'width' => '150px',
        ));

        $this->addColumn('po_order_id', array(
            'header' => Mage::helper('sales')->__('PO #'),
            'type' => 'text',
            'index' => 'po_order_id',
            'width' => '80px',
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_PoLink'
        ));


        $this->addColumn('sup_name', array(
            'header' => Mage::helper('sales')->__('Supplier'),
            'index' => 'sup_name',
        ));

        $this->addColumn('sales_order', array(
            'header' => Mage::helper('sales')->__('Sales order'),
            'type' => 'text',
            'index' => 'po_order_id',
            'width' => '80px',
            'filter' => false,
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_SalesOrder'
        ));

        $this->addColumn('content', array(
            'header' => Mage::helper('AdvancedStock')->__('Content'),
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_PoContentReadOnly',
            'sortable' => false,
            'filter' => false,
        ));

        $this->addColumn('tracking', array(
            'header' => Mage::helper('AdvancedStock')->__('Tracking number'),
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_Tracking',
            'sortable' => false,
            'filter' => false,
            'align' => 'center'
        ));
        
        $this->addColumn('actions', array(
            'header' => Mage::helper('AdvancedStock')->__('Actions'),
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_PoActions',
            'sortable' => false,
            'filter' => false,
            'align' => 'center'
        ));
        

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/ajaxPendingSupplierDeliveryGrid');
    }

}
