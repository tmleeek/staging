<?php

class MDN_Mpm_Model_System_Config_ProductVisibility extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {


    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();
            $this->_options[] = array('value' => '', 'label' => 'All');

            $this->_options[] = array('value' => '1', 'label' => ('Not visible individually'));
            $this->_options[] = array('value' => '2', 'label' => ('Catalog'));
            $this->_options[] = array('value' => '3', 'label' => ('Search'));
            $this->_options[] = array('value' => '4', 'label' => ('Catalog, Search'));

        }
        return $this->_options;
    }

    public function toOptionArray() {
        return $this->getAllOptions();
    }

}
