<?php

class MDN_Mpm_Model_Stat_Segment_Global extends MDN_Mpm_Model_Stat_Segment_Abstract {

    public function getSegmentType()
    {
        return 'global';
    }

    protected function _getOccurrences()
    {
        return array('*' => 'All');
    }

    protected function _getProductIds($occurrence)
    {
        return Mage::getModel('catalog/product')->getCollection()->getAllIds();
    }
}