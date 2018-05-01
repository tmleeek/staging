<?php
/**
 * created : 28 sept. 2009
 * Mandat cash payment method form block
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
 * Mandat cash payment method form block
 * 
 * @package Tatva_MandatCash
 */
class Tatva_MandatCash_Block_Form_MandatCash extends Mage_Payment_Block_Form {
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('payment/form/mandatcash.phtml');
	}
	
	/**
	 * Get CMS block for this payment method
	 * @return string
	 */
	public function getCmsBlock() {
		$blockCode = Mage::getStoreConfig('payment/mandatcash/cmsblock',Mage::app()->getStore()->getId());
		
		$block = Mage::getModel('cms/block')
						->setStoreId(Mage::app()->getStore()->getId())
						->load($blockCode,'identifier');

		return $block->getContent();
	}
}


?>