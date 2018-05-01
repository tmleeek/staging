<?php

class MDN_Mpm_Model_System_Config_NoCompetitorMode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     *
     * @return type
     */
    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();

            $this->_options[] = array('value' => MDN_Mpm_Model_Pricer::kNoCompetitorModeMargin, 'label' => Mage::helper('Mpm')->__('Margin'));
            $this->_options[] = array('value' => MDN_Mpm_Model_Pricer::kNoCompetitorModePrice, 'label' => Mage::helper('Mpm')->__('Price'));
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

    public function toArrayKey()
    {
        $array = array();
        foreach($this->getAllOptions() as $opt)
        {
            $array[$opt['value']] = $opt['label'];
        }
        return $array;
    }

}
