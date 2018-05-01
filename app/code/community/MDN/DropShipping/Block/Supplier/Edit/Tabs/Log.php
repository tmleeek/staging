<?php

class MDN_DropShipping_Block_Supplier_Edit_Tabs_Log extends Mage_Adminhtml_Block_Widget_Grid {

    private $_supplier = null;

    public function __construct() {
        parent::__construct();

        $this->setId('supplierImportLogGrid'); // needed to use getGridUrl
        $this->setUseAjax(true);
        $this->setDefaultSort('dssl_supplier_date', 'desc');
    }

    /**
     * return the log entries for selected supplier
     * @return type 
     */
    protected function _prepareCollection() {

        $collection = Mage::getModel("DropShipping/SupplierLog")
                ->getCollection()
                ->addFieldToFilter( "dssl_supplier_id", $this->getRequest()->getParam("sup_id") );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * return the collumn data
     * @return type 
     */
    protected function _prepareColumns() {

        $this->addColumn('dssl_supplier_date', array(
            'header' => Mage::helper('catalog')->__('Date'),
            'index' => 'dssl_supplier_date',
        ));

        $this->addColumn('dssl_supplier_log', array(
            'header' => Mage::helper('catalog')->__('Log'),
            'index' => 'dssl_supplier_log',
        ));

        $this->addColumn('dssl_duration', array(
            'header' => Mage::helper('catalog')->__('Duration in seconds'),
            'index' => 'dssl_duration',
        ));
        // peut etre changer 0 par non et 1 par oui dans un renderer
        $this->addColumn('dssl_is_error', array(
            'header' => Mage::helper('catalog')->__('Error'),
            'index' => 'dssl_is_error',
            'renderer' => 'MDN_DropShipping_Block_Supplier_Widget_Grid_Column_Renderer_IsError',
        ));
        
        $this->addColumn('dssl_file_name', array(
            'header' => Mage::helper('catalog')->__('File Name'),
            'index' => 'dssl_file_name',
            'renderer' => 'MDN_DropShipping_Block_Supplier_Widget_Grid_Column_Renderer_FileName',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Url to refresh grid using ajax
     */
    public function getGridUrl() { 
        return $this->getUrl('DropShipping/Admin/ImportLogGrid', array('_current'=>true));
    }


    /**
     * get the log entries for the current supplier
     * @return type 
     */
    public function getLogs() {

        $supplierId = $this->getSupplier()->getid();

        return Mage::getModel('DropShipping/SupplierLog')
                        ->getCollection()
                        ->addFieldToFilter("dssl_supplier_id", $supplierId);
    }

    /**
     * call contoller to prune log
     * @return type 
     */
    public function getClearLogUrl() {
        return $this->getUrl('DropShipping/SupplierStockImport/ClearSupplierLog', array('sup_id' => $this->getRequest()->getParam("sup_id")));
    }

}