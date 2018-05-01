<?php
/**
 * created : 18 mai 2010
 * Shipping method form base block
 * @category SQLI
 * @package Tatva_Usa
 * @author alay
 * @copyright SQLI - 2010 - http://www.sqli.com
 */

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Usa_Block_Carrier_Form_Ups extends Tatva_Shipping_Block_Carrier_Form
{

	protected function _construct()
    {
        parent::_construct();
    	$this->setTemplate('carrier/form/ups.phtml');
      			
    }
   
}