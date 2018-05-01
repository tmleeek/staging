<?php
/**
 * created : 28 sept. 2009
 * Mandat cash payment method info block
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Tatva_MandatCash
 * @author ysanchez
 * @copyright SQLI - 2009 - http://www.tatva.com
 */

/**
 * Mandat cash payment method info block
 * 
 * @package Tatva_MandatCash
 */
class Tatva_TransferPayment_Block_Info_TransferPayment extends Mage_Payment_Block_Info
{
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('payment/info/transfer.phtml');
	}
}

?>