<?php
/**
 * created : 28 sept. 2009
 * Transfer payment method form block
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
 * Transfer payment method form block
 * 
 * @package Tatva_TransferPayment
 */
class Tatva_TransferPayment_Block_Info_Mandatadministratif extends Mage_Payment_Block_Info {
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('payment/info/mandatadministratif.phtml');
	}
}
?>