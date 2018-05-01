<?php

class MDN_Mpm_Model_System_Config_Behaviour extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array();

            $this->_options[] = array('value' => 'default', 'label' => Mage::helper('Mpm')->__('Default'));
            $this->_options[] = array('value' => 'normal', 'label' => Mage::helper('Mpm')->__('Conservative'));
            $this->_options[] = array('value' => 'aggressive', 'label' => Mage::helper('Mpm')->__('Moderate'));
            $this->_options[] = array('value' => 'harakiri', 'label' => Mage::helper('Mpm')->__('Aggressive'));
        }

        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    public function toArrayKey()
    {
        $array = array();
        foreach($this->getAllOptions() as $opt) {
            $array[$opt['value']] = $opt['label'];
        }

        return $array;
    }

}
