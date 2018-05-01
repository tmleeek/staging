<?php

/**
 *
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Rule extends Mage_Core_Model_Abstract
{
	
	const COLISSIMO              = 'colissimo';

	/**
	 * Constructor
	 * @return Tatva_Shipping_Model_Rule
	 */
    protected function _construct()
    {
        $this->_init('tatvashipping/rule');
    }

    /**
     * Checks if the rule exists
     * 
     * EXIG TRA-001
     * REG BO-713
     * 
     * @param int $ruleId
     * @param string $shippingCode
     * @param double $weightMin
     * @param double $weightMax
     * @param array $countriesIds
     * @return boolean
     */
    public function exists($ruleId, $shippingCode,$weightMin, $weightMax, $countriesIds){
    	return $this->getResource()->exists($ruleId, $shippingCode,$weightMin, $weightMax,$countriesIds);
    }
    
}

?>