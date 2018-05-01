<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Rate_Result_Method extends Mage_Shipping_Model_Rate_Result_Method
{
		
	private $_amountBeforeDiscount;
	
	public function setAmountBeforeDiscount($value){
		$this->_amountBeforeDiscount = $value;
	}
	
	public function getAmountBeforeDiscount(){
		return $this->_amountBeforeDiscount;
	}
}
