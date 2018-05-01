<?php


/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Area extends Mage_Core_Model_Abstract
{
	
	const FRANCE              = 'F';
	const INTERNATIONAL 	  = 'I';
	const EUROPE 	  		  = 'E';
	
	/**
	 * Constructor
	 * @return Tatva_Shipping_Model_Area
	 */
    protected function _construct()
    {
        $this->_init('tatvashipping/area');
    }	

    /**
     * Vérifie que la zone n'existe pas 
     * 
     * 
     * @param int $areaId
     * @param string $areaCode
     * @return boolean
     */
    public function exists($areaId, $areaCode){
    	return $this->getResource()->exists($areaId, $areaCode);
    }
}

?>