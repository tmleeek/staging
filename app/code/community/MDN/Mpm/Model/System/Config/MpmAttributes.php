<?php

class MDN_Mpm_Model_System_Config_MpmAttributes extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options[] = array('value' => '', 'label' => '');
            try {
                foreach (Mage::helper('Mpm/Carl')->getCatalogFields() as $attribute) {

                    $label = str_replace('attributes.global.', '', $attribute);

                    $this->_options[] = array('value' => $attribute, 'label' => $label);
                }
            } catch(\Exception $e) {
                // The account does not configure
            }

        }
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

}