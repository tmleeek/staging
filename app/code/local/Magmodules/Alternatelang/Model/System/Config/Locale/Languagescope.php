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
 
class Magmodules_Alternatelang_Model_System_Config_Locale_Languagescope extends Mage_Core_Model_Config_Data {

	public function toOptionArray() 
	{
		$scope = array();		
		$scope[] = array('value'=>'', 'label'=> Mage::helper('alternatelang')->__('Include all store views'));
		$scope[] = array('value'=>'website', 'label'=> Mage::helper('alternatelang')->__('All storeviews within a website'));
		$scope[] = array('value'=>'store', 'label'=> Mage::helper('alternatelang')->__('All storeviews within a store'));
		return $scope;
	}
    
}
