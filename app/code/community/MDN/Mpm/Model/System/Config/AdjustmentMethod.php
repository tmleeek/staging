<?php

class MDN_Mpm_Model_System_Config_AdjustmentMethod extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     *
     * @return type
     */
    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();

            $this->_options[] = array('value' => MDN_Mpm_Model_Pricer::kAdjustmentMethodPercent, 'label' => Mage::helper('Mpm')->__('Percent'));
            $this->_options[] = array('value' => MDN_Mpm_Model_Pricer::kAdjustmentMethodValue, 'label' => Mage::helper('Mpm')->__('Value'));
        }
        return $this->_options;
    }

    /**
     *
     * @return type
     */
    public function toOptionArray() {
        return $this->getAllOptions();
    }

}
