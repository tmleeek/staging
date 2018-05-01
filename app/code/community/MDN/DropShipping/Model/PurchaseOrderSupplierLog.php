<?php
class MDN_DropShipping_Model_PurchaseOrderSupplierLog extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('DropShipping/PurchaseOrderSupplierLog');
    }
    
    /**
     * Add a new log
     */
    public function addLog($supplierId, $poId, $salesOrderId)
    {
        
        $obj = Mage::getModel('DropShipping/PurchaseOrderSupplierLog');
        $obj->setdsposl_supplier_id($supplierId);
        $obj->setdsposl_purchase_order_id($poId);
        $obj->setdsposl_sales_order_id($salesOrderId);
        $obj->save();
        
    }
    
}