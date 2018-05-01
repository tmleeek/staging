<?php


/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Apimethod extends Mage_Core_Model_Abstract
{

	/**
	 * Constructor
	 * @return Tatva_Shipping_Model_Area
	 */
    protected function _construct()
    {
        $this->_init('tatvashipping/apimethod');
    }	

    /**
     * Vérifie que la zone n'existe pas 
     * 
     * 
     * @param int $areaId
     * @param string $areaCode
     * @return boolean
     */
    public function exists($shippingId, $shippingmethod)
    {
    	return $this->getResource()->exists($shippingId, $shippingmethod);
    }

    public function getApidetails($shipping_method_code)
    {
        return $this->getResource()->getApidetails($shipping_method_code);
    }
}

?>