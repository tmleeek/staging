<?php

class MDN_Mpm_Block_System_Config_Button_Carl_RepricingConfiguration extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->getLayout()->createBlock('Mpm/Configuration_PricingPerChannels')->setTemplate('Mpm/Configuration/PricingPerChannels.phtml')->toHtml();

    }
}