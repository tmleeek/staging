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
class Tatva_TransferPayment_Block_Form_TransferPayment extends Mage_Payment_Block_Form {
	protected function _construct() {
		parent::_construct();
		$this->setTemplate('payment/form/transfer.phtml');
	}
	
	/**
	 * Get CMS block for this payment method
	 * @return string
	 */
	public function getCmsBlock() {
		$blockCode = Mage::getStoreConfig('payment/transferpayment/cmsblock',Mage::app()->getStore()->getId());
		
		$block = Mage::getModel('cms/block')
						->setStoreId(Mage::app()->getStore()->getId())
						->load($blockCode,'identifier');
		
		$content = $block->getContent();
		
		$content = str_ireplace('{{ribfileurl}}',$this->getRibFileUrl(),$content);
		
		return $content;
	}
	
	/**
	 * Get rib file url
	 * 
	 * @return string
	 */
	public function getRibFileUrl() {
		$ribFile = Mage::app()->getStore()->getConfig('payment/transferpayment/rib_file');
		
		return Mage::getBaseUrl('media')."rib/".$ribFile;
	}
}


?>