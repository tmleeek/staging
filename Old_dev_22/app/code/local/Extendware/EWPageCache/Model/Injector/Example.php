<?php

class Extendware_EWPageCache_Model_Injector_Example extends Extendware_EWPageCache_Model_Injector_Abstract
{
	public function getInjection(array $params = array(), array $request = array()) {
		// can be a block like this
		/*$block = Mage::app()->getLayout()->createBlock('core/template', $this->getId());
		$block->setTemplate('extendware/ewpagecache/welcome/message.phtml');
		$block->setIsLoggedIn(Mage::getSingleton('customer/session')->isLoggedIn());
		$content = $block->toHtml();*/
		
		// can also use the layout system as this can be easier for some people.
		// ensure to add all the relevant handles
		/*Mage::app()->getLayout()->setArea('frontend');
		Mage::app()->getLayout()->getUpdate()->addHandle('default');
		Mage::app()->getLayout()->getUpdate()->load();
		Mage::app()->getLayout()->generateXml()->generateBlocks();
		$block = Mage::app()->getLayout()->getBlock('cart_sidebar');
		$content = $block->toHtml();*/
		
		// or just process it here
		$content = 'example text';
		return $content;
	}
}
