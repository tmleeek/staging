<?php
/* * *************************************************************************************************************************
 *  PurchaseOrderSupplierLog.php
 * ************************************************************************************************************************** */
class MDN_DropShipping_Model_Mysql4_PurchaseOrderSupplierLog extends Mage_Core_Model_Mysql4_Abstract
{
	
    public function _construct()
    {    
        $this->_init('DropShipping/PurchaseOrderSupplierLog', 'dsposl_id');
    }
    
}
?>