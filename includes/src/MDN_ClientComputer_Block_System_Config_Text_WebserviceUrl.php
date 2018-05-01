<?php

class MDN_ClientComputer_Block_System_Config_Text_WebserviceUrl extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $html = Mage::getBaseUrl();
        return $html;
    }

}