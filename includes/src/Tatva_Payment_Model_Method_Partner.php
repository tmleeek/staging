<?php
/**
 * created : 30 sept. 2009
 * Partner Payment Model
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Sqli_Payment
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Payment
 */
class Tatva_Payment_Model_Method_Partner extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'marketplaces_partner';
    protected $_infoBlockType = 'tatvapayment/adminhtml_partner_info';
   
    public function validate()
    {
    	return $this;	
    }
}