<?php

class Tatva_Customerattributes_Model_Mysql4_Customerattributes extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the catalogextensions_id refers to the key field in your database table.
        $this->_init('customerattributes/customerattributes', 'customerattributes_id');
    }
	
	public function makeNewQuoteAttr($attrcode)
	{
		$read = $this->_getReadAdapter();
        $write = $this->_getWriteAdapter();
		$resource = Mage::getSingleton('core/resource');
		$tableName1 = $resource->getTableName('sales/quote');
		$write->query("Alter table ".$tableName1." Add Column ".$attrcode." varchar(255) Default Null;");
	}
	
	public function makeNewQuoteAddAttr($attrcode)
	{
		$read = $this->_getReadAdapter();
        $write = $this->_getWriteAdapter();
		$resource = Mage::getSingleton('core/resource');
		$tableName1 = $resource->getTableName('sales/quote_address');
		$tableName2 = $resource->getTableName('sales/order_address');	
		$write->query("Alter table ".$tableName1." Add Column ".$attrcode." varchar(255) Default Null;");
		$write->query("Alter table ".$tableName2." Add Column ".$attrcode." varchar(255) Default Null;");	
	}
	
	public function deleteQuoteAttr($attrcode)
	{
		$read = $this->_getReadAdapter();
        $write = $this->_getWriteAdapter();
		$resource = Mage::getSingleton('core/resource');	
		$tableName1 = $resource->getTableName('sales/quote');
		$tableName2 = $resource->getTableName('sales/order');
		$write->query("Alter table ".$tableName1." Drop Column ".$attrcode.";");
		$write->query("Alter table ".$tableName2." Drop Column ".$attrcode.";");
	}
	
	public function deleteQuoteAddrAttr($attrcode)
	{
		$read = $this->_getReadAdapter();
        $write = $this->_getWriteAdapter();
		$resource = Mage::getSingleton('core/resource');	
		$tableName1 = $resource->getTableName('sales/quote_address');
		$tableName2 = $resource->getTableName('sales/order_address');
		$write->query("Alter table ".$tableName1." Drop Column ".$attrcode.";");
		$write->query("Alter table ".$tableName2." Drop Column ".$attrcode.";");
	} 
}