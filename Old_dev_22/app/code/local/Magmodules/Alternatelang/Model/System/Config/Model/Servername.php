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
 
class Magmodules_Alternatelang_Model_System_Config_Model_Servername extends Mage_Core_Model_Config_Data {

	public function afterLoad() 
	{
		$servername = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
    	$servername = str_replace(array('http://','https://','www.'), '', $servername);
    	$servername = explode('/', $servername);
    	$servername = $servername[0];
    	$this->setValue($servername);
    }
    
}
