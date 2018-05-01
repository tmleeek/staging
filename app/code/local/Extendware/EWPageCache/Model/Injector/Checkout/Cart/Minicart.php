<?php
class Extendware_EWPageCache_Model_Injector_Checkout_Cart_Minicart extends Extendware_EWPageCache_Model_Injector_Abstract
{
	public function getInjection(array $params = array(), array $request = array()) {
		$data = null;
		$cacheKey = $this->getCacheKey($params);
		$cache = $this->loadFromCache($cacheKey);
		if ($cache !== false) $data = $cache['data'];
		else {
			$type = isset($params['type']) ? $params['type'] : 'checkout/cart_minicart';
			$block = Mage::app()->getLayout()->createBlock($type, $this->getId());

				$sidebar = Mage::app()->getLayout()->createBlock('checkout/cart_sidebar', 'sidebar_' . $this->getId());
				$sidebar->setTemplate('checkout/cart/minicart/items.phtml');
				$sidebar->addItemRender('simple', 'checkout/cart_item_renderer', 'checkout/cart/minicart/default.phtml');
				$sidebar->addItemRender('grouped', 'checkout/cart_item_renderer_grouped', 'checkout/cart/minicart/default.phtml');
				$sidebar->addItemRender('configurable', 'checkout/cart_item_renderer_configurable', 'checkout/cart/minicart/default.phtml');
				$sidebar->addItemRender('bundle', 'checkout/cart_item_renderer_bundle', 'checkout/cart/minicart/default.phtml');
				
				$extra = Mage::app()->getLayout()->createBlock('core/text_list');
					$paypal = Mage::app()->getLayout()->createBlock('paypal/express_shortcut');
					$paypal->setTemplate('paypal/express/minicart/shortcut.phtml');
					$extra->append($paypal);
				$sidebar->setChild('extra_actions', $extra);
				$block->setChild('minicart_content', $sidebar);
			if (empty($params['template']) === true) {
				$params['template'] = 'checkout/cart/minicart.phtml';
			}
			$block->setTemplate($params['template']);
			$data = $block->toHtml();
			$this->saveToCache($cacheKey, $data);
		}
		return $data;
	}
}
