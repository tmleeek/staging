<?php

class MDN_Mpm_Block_System_Config_Button_Carl_Ip extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $url = 'http://bms-performance.com/ip.php';
        $ctx = stream_context_create(array('http'=>
            array(
                'timeout' => 5,
            )
        ));

        $html = file_get_contents($url, false, $ctx);

        return $html;
    }
}
