<?php

class MDN_Mpm_Model_System_Config_CompeteWith extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     *
     * @return type
     */
    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();
            $this->_options[] = array('value' => '', 'label' => '');

            $this->_options[] = array('value' => MDN_Mpm_Model_Pricer::kCompeteWithBestPrice, 'label' => Mage::helper('Mpm')->__('Best price'));
            $this->_options[] = array('value' => MDN_Mpm_Model_Pricer::kCompeteWithBestRank, 'label' => Mage::helper('Mpm')->__('Best rank'));
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
