<?php
/**
 * created : 19 mai 2010
 * 
 * @category SQLI
 * @package Tatva_Usa
 * @author alay
 * @copyright SQLI - 2010 - http://www.sqli.com
 */

/**
 * 
 * @package Tatva_Usa
 */
class Tatva_Usa_Block_Rule_Ups extends Tatva_Shipping_Block_Rule_Abstract
{
    
    public function getRules(){
		$this->_rules = Mage::getModel('tatvashipping/rule')->getCollection()
      			->addShippingFilter(Tatva_Shipping_Model_Rule::UPS)
      			->addAreaFilter($this->getZone())
      			->addAreaOrder();
      	return $this->_rules;
    }
}