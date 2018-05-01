<?php
/* * *************************************************************************************************************************
 *  SupplierFile.php
 * ************************************************************************************************************************** */
class MDN_DropShipping_Model_Mysql4_SupplierFile extends Mage_Core_Model_Mysql4_Abstract
{
	
    public function _construct()
    {    
        $this->_init('DropShipping/SupplierFile', 'dssf_id');
    }
    
}
?>