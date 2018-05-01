<?php

class MDN_Mpm_Model_System_Config_Channels extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     *
     * @return type
     */
    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();

            $this->_options = Mage::helper('Mpm/Carl')->getChannelsSubscribed(true);
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
