<?php

class MDN_DropShipping_Block_Tabs_PendingTracking extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();
        $this->setId('dropShippPendingTracking');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(__('No items'));
        $this->setSaveParametersInSession(true);
        $this->setRowClickCallback(false);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $dropShippingOrderIds = mage::helper('DropShipping')->getPendingTrackingOrderIds();

        $prefix = Mage::getConfig()->getTablePrefix();

        $collection = mage::getModel('sales/order')
                        ->getCollection()
                        ->addFieldToFilter('entity_id', array('in' => $dropShippingOrderIds));

        $collection->getSelect()->joinLeft(array('s1' => $prefix . 'sales_flat_order_address'), 'main_table.shipping_address_id = s1.entity_id', array('firstname', 'lastname'));
        $collection->getSelect()->joinLeft(array('s2' => $prefix . 'sales_flat_order_address'), 'main_table.billing_address_id = s2.entity_id', array('firstname', 'lastname'));
        $collection->getSelect()->columns(new Zend_Db_Expr("CONCAT(s1.firstname, ' ',s2.lastname) AS shipping_name"));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('increment_id', array(
            'header' => Mage::helper('sales')->__('Order #'),
            'type' => 'number',
            'index' => 'increment_id',
            'width' => '80px',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '150px',
        ));

        //Organizer
        $this->addColumn('organizer', array(
            'header' => Mage::helper('Organizer')->__('Organizer'),
            'renderer' => 'MDN_Organizer_Block_Widget_Column_Renderer_Comments',
            'align' => 'center',
            'entity' => 'order',
            'filter' => false,
            'sort' => false
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
            'width' => '250px',
            'filter_index' => "CONCAT(s1.firstname, ' ',s2.lastname)"
        ));

        $this->addColumn('tracking', array(
            'header' => Mage::helper('AdvancedStock')->__('Tracking number'),
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_Tracking',
            'sortable' => false,
            'filter' => false
        ));        

        $this->addColumn('suppliers', array(
            'header' => Mage::helper('AdvancedStock')->__('Supplier(s)'),
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_Supplier',
            'sortable' => false,
            'filter' => false
        ));     
        
        $this->addColumn('purchase_orders', array(
            'header' => Mage::helper('AdvancedStock')->__('Purchase order(s)'),
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_PurchaseOrder',
            'sortable' => false,
            'filter' => false
        ));    
        
        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                    array(
                        'header' => Mage::helper('sales')->__('Action'),
                        'width' => '50px',
                        'type' => 'action',
                        'getter' => 'getId',
                        'actions' => array(
                            array(
                                'caption' => Mage::helper('sales')->__('View'),
                                'url' => array('base' => 'adminhtml/sales_order/view'),
                                'field' => 'order_id'
                            )
                        ),
                        'filter' => false,
                        'sortable' => false,
                        'index' => 'stores',
                        'is_system' => true,
            ));
        }

        return parent::_prepareColumns();
    }

    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }

}
