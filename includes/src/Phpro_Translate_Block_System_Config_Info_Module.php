<?php

class Phpro_Translate_Block_System_Config_Info_Module extends Mage_Adminhtml_Block_System_Config_Form_Field {

    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;
        
        return $modulesArray['Phpro_Translate']->version;
    }
}