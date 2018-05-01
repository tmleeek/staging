<?php

/**
 *
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Mysql4_Marketmethod extends Mage_Core_Model_Mysql4_Abstract
{
	
	/**
	 * Constructor
	 */
    protected function _construct()
    {
        $this->_init('tatvashipping/shipping_marketmethod', 'shipping_marketmethod_id');
    }
    

	/**
     * Vérifie que la zone n'existe pas 
     * 
     * 
     * @param int $areaId
     * @param string $shippingmethod
     * @return boolean
     */
    public function exists($method_id, $shipping_code_amazon, $shipping_code_ebay, $weight_from, $weight_to, $total_from, $total_to,$market_shipping_code,$countries_ids)
    {
    	$select = $this->_getReadAdapter ()
    			->select ()
    			->from(array('marketmethod'=>$this->getMainTable()))
    	  		->where('shipping_code_amazon = ?',$shipping_code_amazon)
                ->where('shipping_code_ebay = ?',$shipping_code_ebay)
                ->where('market_weight_from = ?',$weight_from)
                ->where('market_weight_to = ?',$weight_to)
                ->where('market_ordertotal_from = ?',$total_from)
                ->where('market_ordertotal_to = ?',$total_to)
                ->where('market_shipping_code = ?',$market_shipping_code)
                ->where('countries_ids = ?',$countries_ids);
     	if($method_id){
     		$select->where('marketmethod.shipping_marketmethod_id != ?', $method_id);
     	}

    	$data = $this->_getReadAdapter()->fetchAll($select);
    	$count = 0;
    	if($data && is_array($data)){
    		$count = sizeof($data);
    	}
    	return  $count > 0 ? true : false;
     }

     public function getMarketdetails($shipping_method_code)
    {

    	$select = $this->_getReadAdapter ()
    			->select ()
    			->from(array('marketmethod'=>$this->getMainTable()))
    	  		->where('shipping_method_code = ?',$shipping_method_code);

    	$data = $this->_getReadAdapter()->fetchRow($select);

    	return  $data;
     }
}

?>