<?php

class MDN_Mpm_Model_System_Config_ShippingCalculation extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {


    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();
            $this->_options[] = array('value' => '', 'label' => '');

            $this->_options[] = array('value' => MDN_Mpm_Model_Pricer::kShippingCalculationMethodPercentage, 'label' => 'Percentage');
            $this->_options[] = array('value' => MDN_Mpm_Model_Pricer::kShippingCalculationMethodFixed, 'label' => 'Fixed');

        }
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

}
