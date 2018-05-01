<?php

class MDN_Mpm_Block_System_Config_Button_Carl_Verification extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $valid = Mage::helper('Mpm/Carl')->checkCredentials();
        if ($valid)
            $html = '<font color="green"><b>'.$this->__('Login correct').'</b></font>';
        else
            $html = '<font color="red"><b>'.$this->__('Login incorrect').'</b></font>';

        return $html;
    }
}