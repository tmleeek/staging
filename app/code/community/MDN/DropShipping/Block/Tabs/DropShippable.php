<?php

class MDN_DropShipping_Block_Tabs_DropShippable extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {

        parent::__construct();
        $this->setId('dropshippableGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->_parentTemplate = $this->getTemplate();
        $this->setEmptyText(__('No items'));
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setRowClickCallback(false);
    }

    protected function _prepareCollection() {
        $dropShippingOrderIds = mage::helper('DropShipping')->getDropShippingOrderIds();
        
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

                
        //Organizer
        $this->addColumn('organizer', array(
            'header' => Mage::helper('Organizer')->__('Organizer'),
            'renderer' => 'MDN_Organizer_Block_Widget_Column_Renderer_Comments',
            'align' => 'center',
            'entity' => 'order',
            'filter' => false,
            'sort' => false,
            'width' => '80px',
        ));
        
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('sales')->__('Order'),
            'type' => 'text',
            'index' => 'increment_id',
            'width' => '80px',
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_OrderLink'
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '150px',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));



        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Customer'),
            'index' => 'shipping_name',
            'width' => '250px',
            'filter_index' => "CONCAT(s1.firstname, ' ',s2.lastname)"
        ));

        $this->addColumn('Products', array(
            'header' => Mage::helper('AdvancedStock')->__('Products'),
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_OrderContent',
            'sortable' => false,
            'filter' => false,
            'dropship_status_restriction' => array('', null),
            'div_prefix' => 'dropshippable_content_'
        ));

        $this->addColumn('dropshippable_action', array(
            'header' => Mage::helper('AdvancedStock')->__('Action'),
            'renderer' => 'MDN_DropShipping_Block_Widget_Grid_Column_Renderer_DropShippableAction',
            'sortable' => false,
            'filter' => false,
            'align' => 'center'
        ));

        return parent::_prepareColumns();
    }

    /*
    public function getGridParentHtml() {
        $templateName = Mage::getDesign()->getTemplateFilename($this->_parentTemplate, array('_relative' => true));
        return $this->fetchView($templateName);
    }
     * 
     */
    

    public function getGridUrl() {
        return $this->getUrl('*/*/ajaxDropShippableGrid');
    }

}
