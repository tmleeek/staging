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
 
class Magmodules_Alternatelang_Block_Head extends Mage_Core_Block_Template {

    protected function _construct() 
    {
        parent::_construct();	
		if(Mage::getStoreConfig('alternatelang/general/enabled'))
        {
			$this->setTemplate('magmodules/alternatelang/head.phtml');
		}	
    }

    public function getAlternateData() 
    {
		return $this->helper('alternatelang')->getAlternateData();
    }
    
    public function getAlternateScope() 
    {
		return Mage::getStoreConfig('alternatelang/targeting/language_scope'); 			
    }

    public function getAlternateDebug() 
    {
		if(Mage::getStoreConfig('alternatelang/config/debug'))
        {
			if(Mage::app()->getRequest()->getParam('show-alternate'))
            {
				return true;
			}
		}		
    }    
    
}