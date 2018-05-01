<?php
/**
 * created : 28 sept. 2009
 * Transfer payment method form block
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Sqli_TransferPayment
 * @author ysanchez
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * Transfer payment method form block
 * 
 * @package Sqli_TransferPayment
 */
class Mage_Cybermutforeign_Block_Info extends Mage_Payment_Block_Info {
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('cybermutforeign/info.phtml');
	}
}
?>