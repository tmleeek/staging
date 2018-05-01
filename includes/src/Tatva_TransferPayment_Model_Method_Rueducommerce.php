<?php
/**
 * created : 28 sept. 2009
 * Transfer payment method model
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Tatva_TransferPayment
 * @author ysanchez
 * @copyright SQLI - 2009 - http://www.tatva.com
 */

/**
 * Transfer cash payment method model
 * 
 * @package Tatva_TransferPayment
 */
class Tatva_TransferPayment_Model_Method_Rueducommerce extends Mage_Payment_Model_Method_Abstract {
	const CODE = 'rueducommerce';
	
	protected $_code  = 'rueducommerce';
	protected $_formBlockType = 'transferpayment/form_rueducommerce';
	protected $_infoBlockType = 'transferpayment/info_rueducommerce';
}

?>