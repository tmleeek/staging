<?php

class MDN_DropShipping_Block_Supplier_Edit_Tabs_StockPriceImport extends Mage_Adminhtml_Block_Widget_Form {

    private $_supplier = null;

    /**
     * 
     */
    public function __construct() {
        parent::__construct();
        $sup_id = Mage::app()->getRequest()->getParam('sup_id', false);
        $model = Mage::getModel('Purchase/Supplier');
        $this->_supplier = $model->load($sup_id);
        $this->setTemplate('DropShipping/Supplier/Edit/Tab/StockPriceImport.phtml');
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

    /**
     * 
     * @return type
     */
    public function getMatchModes() {
        return array('sku' => 'sku', 'supplier_sku' => 'Supplier sku');
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

}