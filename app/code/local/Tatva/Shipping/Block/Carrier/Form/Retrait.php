<?php
/**
 * created : 9 déc 2009
 * Shipping method form base block
 * @category SQLI
 * @package Sqli_Shipping
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Shipping
 */
class Tatva_Shipping_Block_Carrier_Form_Retrait extends Ttava_Shipping_Block_Carrier_Form
{

	protected function _construct()
    {
        parent::_construct();
    	$this->setTemplate('carrier/form/retrait.phtml');
      			
    }
   
}