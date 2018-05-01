<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Rule_Colissimocityssimo extends Tatva_Shipping_Block_Rule_Abstract
{

    public function getRules(){
		$this->_rules = Mage::getModel('tatvashipping/rule')->getCollection()
      			->addShippingFilter(Tatva_Shipping_Model_Rule::COLISSIMO)
      			->addAreaFilter($this->getZone())
      			->addAreaOrder();
      	return $this->_rules;
    }
}