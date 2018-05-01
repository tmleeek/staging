<?php


/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Marketmethod extends Mage_Core_Model_Abstract
{

	/**
	 * Constructor
	 * @return Tatva_Shipping_Model_Area
	 */
    protected function _construct()
    {
        $this->_init('tatvashipping/marketmethod');
    }	

    /**
     * Vérifie que la zone n'existe pas 
     * 
     * 
     * @param int $areaId
     * @param string $areaCode
     * @return boolean
     */
    public function exists($method_id, $shipping_code_amazon, $shipping_code_ebay, $weight_from, $weight_to, $total_from, $total_to,$market_shipping_code,$countries_ids)
    {
    	return $this->getResource()->exists($method_id, $shipping_code_amazon, $shipping_code_ebay, $weight_from, $weight_to, $total_from, $total_to,$market_shipping_code,$countries_ids);
    }

    public function getMarketdetails($shipping_method_code)
    {
        return $this->getResource()->getMarketdetails($shipping_method_code);
    }
}

?>