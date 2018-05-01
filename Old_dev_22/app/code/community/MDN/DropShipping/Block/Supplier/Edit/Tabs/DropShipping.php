<?php

class MDN_DropShipping_Block_Supplier_Edit_Tabs_DropShipping extends Mage_Adminhtml_Block_Widget_Form {

    private $_supplier = null;

    /**
     * 
     */
    public function __construct() {
        parent::__construct();
        $sup_id = Mage::app()->getRequest()->getParam('sup_id', false);
        $model = Mage::getModel('Purchase/Supplier');
        $this->_supplier = $model->load($sup_id);
        $this->setTemplate('DropShipping/Supplier/Edit/Tab/DropShipping.phtml');
    }

    /**
     * @return unknown
     */
    public function getSupplier() {
        return $this->_supplier;
    }

    /**
     * 
     */
    public function getAttachmentTypes() {
        return Mage::helper('DropShipping/Attachment')->getAttachmentTypes();
    }

    /**
     * 
     * @return type
     */
    public function getTemplateCodes()
    {
        $codes = array();
        $lastShipment = Mage::getModel('sales/order_shipment')->getCollection()->addAttributeToSelect('*')->getLastItem();
        $codes = Mage::getSingleton('DropShipping/ExportType_Csv')->getCodes($lastShipment, $this->getSupplier());
        return $codes;
    }
    
    /**
     * 
     * @return type
     */
    public function getPreview()
    {
        $lastShipment = Mage::getModel('sales/order_shipment')->getCollection()->addAttributeToSelect('*')->getLastItem();
        $po = Mage::getModel('Purchase/Order');
        $po->setpo_sup_num($this->getSupplier()->getId());
        return Mage::getSingleton('DropShipping/ExportType_Csv')->getContent($po, array($lastShipment));
    }

}