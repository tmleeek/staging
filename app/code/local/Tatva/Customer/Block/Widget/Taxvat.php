<?php
/**
 * created : 2 sept. 2009
 * 
 * @category SQLI
 * @package Sqli_Customer
 * @author emchaabelasri
 * @copyright SQLI - 2009 - http://www.sqli.com
 * 
 * EXIG CLI-002
 */

/**
 * Description of the class
 * @package Sqli_Customer
 */
class Tatva_Customer_Block_Widget_Taxvat  extends  Mage_Customer_Block_Widget_Taxvat  {

    public function _construct(){
        parent::_construct();
        $this->setTemplate('tatva/customer/widget/taxvat.phtml');
    }

    public function isEnabled(){
        return true;
    }
	
}
