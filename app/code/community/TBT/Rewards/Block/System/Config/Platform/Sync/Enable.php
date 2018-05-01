<?php

class TBT_Rewards_Block_System_Config_Platform_Sync_Enable extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * If account not connected to Platform we'll disable the select box
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = parent::_getElementHtml($element);

        $isConnected = Mage::getStoreConfig('rewards/platform/is_connected');
        if ($isConnected) {
            return $html;
        }

        $bits = explode('class=" select">', $html);
        $html = "{$bits[0]} disabled>" . $bits[1];

        return $html;
    }

}