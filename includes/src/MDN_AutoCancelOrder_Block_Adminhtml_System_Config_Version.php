<?php

class MDN_AutoCancelOrder_Block_Adminhtml_System_Config_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return type 
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;
        $html = $modulesArray['MDN_AutoCancelOrder']->version;
        return $html;
    }
}