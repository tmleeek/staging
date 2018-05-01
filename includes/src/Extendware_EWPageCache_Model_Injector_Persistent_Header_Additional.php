<?php
class Extendware_EWPageCache_Model_Injector_Persistent_Header_Additional extends Extendware_EWPageCache_Model_Injector_Abstract
{
	public function getInjection(array $params = array(), array $request = array()) {
		$data = null;
		$cacheKey = $this->getCacheKey($params);
		$cache = $this->loadFromCache($cacheKey);
		if ($cache !== false) $data = $cache['data'];
		else {
			$type = isset($params['type']) ? $params['type'] : 'persistent/header_additional';
			$block = Mage::app()->getLayout()->createBlock($type, $this->getId());
			$block->setTemplate($params['template']);
			$data = $block->toHtml();
			$this->saveToCache($cacheKey, $data);
		}
		return $data;
	}
}
