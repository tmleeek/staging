<?php

class TBT_Rewards_Block_System_Config_Platform_Disconnect extends TBT_Rewards_Block_System_Config_Abstractbutton
{
    public function getButtonData($buttonBlock)
    {
        $url = Mage::helper('adminhtml')->getUrl('rewardsadmin/manage_config_platform/disconnect');
        $data = array(
            'label'     => Mage::helper('rewards')->__("Disconnect from Sweet Tooth"),
            'onclick'   => "confirmSetLocation(
                '{$this->__('Disconnecting your account will cause Sweet Tooth to stop rewarding you customers, if not re-connected within 24 hours.\nAre you sure you want to do this?')}',
                '{$url}'
            )",
            'class'     => "",
            'comment'	=> "",
        );

        return $data;
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '';

        if (Mage::getStoreConfig('rewards/platform/is_connected')) {
            $html = parent::render($element);
        }

        return $html;
    }
}
