<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Alternatelang
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2016 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Alternatelang_Model_System_Config_Locale_Suggest extends Mage_Core_Model_Config_Data {

	public function afterLoad() 
	{
		if($store =  Mage::app()->getRequest()->getParam('store')) {    	
			$store_id = Mage::getModel('core/store')->load($store)->getId();   	
			$language = Mage::getStoreConfig('general/locale/code', $store_id);		
			$sub1 = substr($language, 0, 2);
			$sub2 = strtolower(substr($language, 3, 4));		 
			$text = $sub1 . ' or ' . $sub1 . '-' . $sub2;
		} else {
			$text =  Mage::helper('alternatelang')->__('-- please select store scope --');	
		}
    	$this->setValue($text);
    }
    
}
