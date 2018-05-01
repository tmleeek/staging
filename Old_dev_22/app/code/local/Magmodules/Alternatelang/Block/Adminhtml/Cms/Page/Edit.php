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

if (Mage::helper('core')->isModuleEnabled('Bubble_CmsTree')) {
    class Magmodules_Alternatelang_Block_Adminhtml_Cms_Page_Edit_Abstract extends Bubble_CmsTree_Block_Adminhtml_Cms_Page_Edit {};
} else {
    class Magmodules_Alternatelang_Block_Adminhtml_Cms_Page_Edit_Abstract extends Mage_Adminhtml_Block_Cms_Page_Edit {};
}

class Magmodules_Alternatelang_Block_Adminhtml_Cms_Page_Edit extends Magmodules_Alternatelang_Block_Adminhtml_Cms_Page_Edit_Abstract {

    public function __construct() 
    {
        parent::__construct();
		if(Mage::getStoreConfig('alternatelang/config/cms_categories')) {		       
			$this->_formScripts[] = " 
				category_new();
			";
		}	
    }

}
