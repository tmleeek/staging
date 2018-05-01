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
class MDN_DropShipping_Block_Supplier_Edit_Tabs_Ftp extends Mage_Adminhtml_Block_Widget_Form {

    private $_supplier = null;

    /**
     * 
     */
    public function __construct() {
        parent::__construct();
        $sup_id = Mage::app()->getRequest()->getParam('sup_id', false);
        $model = Mage::getModel('Purchase/Supplier');
        $this->_supplier = $model->load($sup_id);
        $this->setTemplate('DropShipping/Supplier/Edit/Tab/FtpAccount.phtml');
    }

    /**
     * @return unknown
     */
    public function getSupplier() {
        return $this->_supplier;
    }

    /**
     * call SupplierStockimportController ::  ImportAction()
     * host, port, login, password
     */
    public function getImportStockUrl() {

        return $this->getUrl('DropShipping/SupplierStockImport/Import', array("sup_id" => $this->getSupplier()->getsup_id()));
    }

    /*
     * get all option of warhouse
     */

    public function getWarehouses() {
        $options = array();
        $suppliers = mage::getModel('AdvancedStock/Warehouse')->getCollection();
        foreach ($suppliers as $supplier) {
            $options[] = array(
                'value' => $supplier->getId(),
                'label' => $supplier->getstock_name(),
            );
        }
        return $options;
    }

    /**
     *
     * @return type 
     */
    public function getTestUrl() {
        return $this->getUrl('DropShipping/Test/SupplierFile', array("sup_id" => $this->getSupplier()->getsup_id()));
    }

    /**
     * 
     */
    public function getAttachmentTypes() {
        return Mage::helper('DropShipping/Attachment')->getAttachmentTypes();
    }

}