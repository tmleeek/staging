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
 
class Magmodules_Alternatelang_Block_Adminhtml_System_Config_Form_Field_Version extends Mage_Adminhtml_Block_System_Config_Form_Field {

    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element) 
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;        
        return $modulesArray['Magmodules_Alternatelang']->version;
    }
    
}