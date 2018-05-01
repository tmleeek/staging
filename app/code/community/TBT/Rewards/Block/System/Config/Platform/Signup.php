<?php

class TBT_Rewards_Block_System_Config_Platform_Signup extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);
        
        $bits = explode('/>', $html);
        $html = "{$bits[0]} autocomplete='off' />";
        
        return $html;
    }
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        if (!Mage::getStoreConfig('rewards/platform/is_connected')) {
            return parent::render($element);
        }
        
        return '';
    }
}
