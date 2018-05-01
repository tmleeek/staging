<?php

class MDN_Mpm_Model_System_Config_CatalogExportFormat extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array();

            $this->_options[] = array('value' => self::FORMAT_XML, 'label' => Mage::helper('Mpm')->__('XML'));
            $this->_options[] = array('value' => self::FORMAT_JSON, 'label' => Mage::helper('Mpm')->__('JSON'));
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
