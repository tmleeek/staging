<?php

class MDN_Mpm_Model_System_Config_ProductType extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {


    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();
            $this->_options[] = array('value' => '', 'label' => 'All');

            foreach(Mage::getSingleton('catalog/product_type')->getOptionArray() as $type => $label)
            {
                $this->_options[] = array('value' => $type, 'label' => $label);
            }

        }
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

}
