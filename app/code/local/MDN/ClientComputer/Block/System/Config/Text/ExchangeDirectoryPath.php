<?php

class MDN_ClientComputer_Block_System_Config_Text_ExchangeDirectoryPath extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	$html = '';
    	try 
    	{
			$html = '<font color="green">'.mage::helper('ClientComputer')->getExchangeDirectory().'</font>';	
    	}
    	catch (Exception $ex)
    	{
			$html = '<font color="red">'.$ex->getMessage().'</font>';
    	}
        return $html;
    }
}