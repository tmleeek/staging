<?php

class MDN_DropShipping_Model_System_Config_Source_PriceResponseConfirmationAction extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    protected $_options;

    public function toOptionArray() {

        if (!$this->_options) {
            $this->getAllOptions();
        }
        return $this->_options;
    }

    public function getAllOptions() {
        if (!$this->_options) {
            
            $this->_options = array();

            $this->_options[] = array(
                'value' => 'dropship_request',
                'label' => 'Drop ship request'
            );

            $this->_options[] = array(
                'value' => 'dropship',
                'label' => 'Drop ship'
            );
            
            
        }
        return $this->_options;
    }

}