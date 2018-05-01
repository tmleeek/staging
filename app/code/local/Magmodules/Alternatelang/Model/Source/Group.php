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
 
class Magmodules_Alternatelang_Model_Source_Group {

	public function toOptionArray() 
	{
		$group = array();
		$group[] = array('value'=> '1', 'label'=> Mage::helper('alternatelang')->__('Group 1'));
		$group[] = array('value'=> '2', 'label'=> Mage::helper('alternatelang')->__('Group 2'));	
		$group[] = array('value'=> '3', 'label'=> Mage::helper('alternatelang')->__('Group 3'));	
		$group[] = array('value'=> '4', 'label'=> Mage::helper('alternatelang')->__('Group 4'));	
		return $group;
	}
	
}