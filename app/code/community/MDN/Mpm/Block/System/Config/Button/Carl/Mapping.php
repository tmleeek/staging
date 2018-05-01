<?php

class MDN_Mpm_Block_System_Config_Button_Carl_Mapping extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        try
        {
            return $this->getLayout()->createBlock('Mpm/Configuration_Mapping')->setTemplate('Mpm/Configuration/FieldsToMap.phtml')->toHtml();
        }
        catch(Exception $ex)
        {
            return $ex->getMessage();
        }
    }
}