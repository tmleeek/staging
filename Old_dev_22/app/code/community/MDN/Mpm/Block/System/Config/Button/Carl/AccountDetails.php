<?php

class MDN_Mpm_Block_System_Config_Button_Carl_AccountDetails extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $html = '<ul>';

        $details = Mage::helper('Mpm/Carl')->getAccountDetails();
        foreach($details as $k => $v)
        {
            $html .= '<li>'.$k.' : '.$v.'</li>';
        }

        $html = '</ul>';

        return $html;
    }
}