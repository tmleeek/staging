<?php

class MDN_ClientComputer_Block_Adminhtml_Form_Field_FtpExchangeDirectoryAdvice extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $useContainerId = $element->getData('use_container_id');
        $html = '<tr id="row_' . $id . '">'
              . '<td class="label"><label for="'.$id.'">'.$element->getLabel().'</label></td>';
    	
        $html.= '<td class="value">';
        $html .= Mage::getBaseDir('var');
        $html .= '</td>';
        $html .= '</tr>';
              
    	return $html;
    }
}
