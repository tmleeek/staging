<?php


/**
 * 
 * @package Tatva_Shipping
 */
abstract class Tatva_Shipping_Block_Rule_Abstract extends Mage_Core_Block_Template
{
	protected $_order;
	protected $_carrier;
	protected $_rules = array();
	
    protected function _construct()
    {
        $this->setTemplate('carrier/rule.phtml');
        parent::_construct();
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
 	public function getRules(){
 		return $this->_rules;
 	}
 	
 	public function getPrice($price){
 		return Mage::app()->getStore()->convertPrice($this->helper('tax')->getShippingPrice($price, true, null), true);
 	}
 
}