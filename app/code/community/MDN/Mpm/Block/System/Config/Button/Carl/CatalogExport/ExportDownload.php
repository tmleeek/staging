<?php

class MDN_Mpm_Block_System_Config_Button_Carl_CatalogExport_ExportDownload extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('adminhtml/Mpm_Carl/ExportDownloadCatalog');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel(Mage::helper('Mpm')->__('Download'))
            ->setOnClick("setLocation('$url')")
            ->toHtml();

        return $html;
    }
}