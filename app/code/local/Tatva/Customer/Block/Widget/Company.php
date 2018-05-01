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
 * REG CLI-105
 */

/**
 * Description of the class
 * @package Sqli_Customer
 */
class Tatva_Customer_Block_Widget_Company  extends  Mage_Customer_Block_Widget_Abstract  {

    public function _construct(){
        parent::_construct();
        $this->setTemplate('customer/widget/name.phtml');
    }

}