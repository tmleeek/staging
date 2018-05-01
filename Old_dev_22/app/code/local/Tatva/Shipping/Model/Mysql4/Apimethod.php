<?php

/**
 *
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Mysql4_Apimethod extends Mage_Core_Model_Mysql4_Abstract
{
	
	/**
	 * Constructor
	 */
    protected function _construct()
    {
        $this->_init('tatvashipping/shipping_apimethod', 'shipping_apimethod_id');
    }
    

	/**
     * Vérifie que la zone n'existe pas 
     * 
     * 
     * @param int $areaId
     * @param string $shippingmethod
     * @return boolean
     */
    public function exists($shippingId, $shippingmethod)
    {
    	$select = $this->_getReadAdapter ()
    			->select ()
    			->from(array('apimethod'=>$this->getMainTable()))
    	  		->where('shipping_method_code = ?',$shippingmethod);
     	if($shippingId){
     		$select->where('apimethod.shipping_apimethod_id != ?', $shippingId);
     	}

     	
    	$data = $this->_getReadAdapter()->fetchAll($select);
    	$count = 0;
    	if($data && is_array($data)){
    		$count = sizeof($data);
    	}
    	return  $count > 0 ? true : false;
     }

     public function getApidetails($shipping_method_code)
    {

    	$select = $this->_getReadAdapter ()
    			->select ()
    			->from(array('apimethod'=>$this->getMainTable()))
    	  		->where('shipping_method_code = ?',$shipping_method_code);

    	$data = $this->_getReadAdapter()->fetchRow($select);

    	return  $data;
     }
}

?>