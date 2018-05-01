<?php

class MDN_Mpm_Block_System_Config_Button_Carl_ProductsExportedCount extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $count = count(Mage::getModel('Mpm/Export_Catalog')->getProductIds());

        return $count.'';
    }

}