<?php

class MDN_Mpm_Model_Stat_Segment_Supplier extends MDN_Mpm_Model_Stat_Segment_Abstract {

    public function getSegmentType()
    {
        return 'Supplier';
    }

    protected function _getOccurrences()
    {
        $occurrences = array();

        $values = Mage::helper('Mpm/Attribute')->getAttributeValues($this->getAttributeCode(), false);
        foreach($values as $item)
        {
            $occurrences[$item['value']] = $item['label'];
        }
        return $occurrences;
    }

    protected function _getProductIds($manufacturerId)
    {
        return Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter($this->getAttributeCode(), $manufacturerId)->getAllIds();
    }

    protected function getAttributeCode()
    {
        return Mage::getStoreConfig('mpm/misc/supplier_attribute');
    }
}