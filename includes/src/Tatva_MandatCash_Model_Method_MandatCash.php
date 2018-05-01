<?php
/**
 * created : 28 sept. 2009
 * Mandat cash payment method model
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
 * Mandat cash payment method model
 * 
 * @package Tatva_MandatCash
 */
class Tatva_MandatCash_Model_Method_MandatCash extends Mage_Payment_Model_Method_Abstract {
	protected $_code  = 'mandatcash';
	protected $_formBlockType = 'mandatcash/form_mandatCash';
	protected $_infoBlockType = 'mandatcash/info_mandatCash';
}

?>