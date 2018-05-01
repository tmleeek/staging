<?php

class MDN_DropShipping_Model_Source_Config_DefaultDropShipMode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions() {
        if (!$this->_options) {

            foreach (Mage::helper('DropShipping')->getDropShipMode() as $value => $label) {

                $this->_options[$value] = $label;
            }
        }
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

}
