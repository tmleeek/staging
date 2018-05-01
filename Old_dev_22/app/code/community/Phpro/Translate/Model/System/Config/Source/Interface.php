<?php

class Phpro_Translate_Model_System_Config_Source_Interface {

    public function toOptionArray($isMultiSelect) {
        $options = array(
            array('value' => 'frontend', 'label' => Mage::helper('adminhtml')->__('Frontend')),
            array('value' => 'adminhtml', 'label' => Mage::helper('adminhtml')->__('Admin HTML'))
        );
        return $options;
    }

    public function toArray() {
        $options = array(
            'frontend' => Mage::helper('adminhtml')->__('Frontend'),
            'adminhtml' => Mage::helper('adminhtml')->__('Admin HTML')
        );
        return $options;
    }

}

