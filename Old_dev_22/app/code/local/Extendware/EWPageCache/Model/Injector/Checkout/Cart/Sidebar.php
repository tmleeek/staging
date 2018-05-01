<?php
class Extendware_EWPageCache_Model_Injector_Checkout_Cart_Sidebar extends Extendware_EWPageCache_Model_Injector_Abstract
{
	public function getInjection(array $params = array(), array $request = array()) {
		$data = null;
		$cacheKey = $this->getCacheKey($params);
		$cache = $this->loadFromCache($cacheKey);
		if ($cache !== false) $data = $cache['data'];
		else {
			$type = isset($params['type']) ? $params['type'] : 'checkout/cart_sidebar';
			$block = Mage::app()->getLayout()->createBlock($type, $this->getId());
			$block->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/sidebar/default.phtml');
			$block->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/sidebar/default.phtml');
			$block->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/sidebar/default.phtml');
			
			$extra = Mage::app()->getLayout()->createBlock('core/text_list');
				$paypal = Mage::app()->getLayout()->createBlock('paypal/express_shortcut');
				$paypal->setTemplate('paypal/express/minicart/shortcut.phtml');
				$extra->append($paypal);
			$block->setChild('extra_actions', $extra);
				
			if (empty($params['template']) === true) {
				$params['template'] = 'checkout/cart/sidebar.phtml';
			}
			$block->setTemplate($params['template']);
			$data = $block->toHtml();
			$this->saveToCache($cacheKey, $data);
		}
		return $data;
	}
}
