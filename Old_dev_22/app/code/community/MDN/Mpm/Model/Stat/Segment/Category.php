<?php

class MDN_Mpm_Model_Stat_Segment_Category extends MDN_Mpm_Model_Stat_Segment_Abstract {

    public function getSegmentType()
    {
        return 'category';
    }

    protected function _getOccurrences()
    {
        $occurrences = array();

        $collection = Mage::getModel('catalog/category')->getCollection()->setOrder('path');
        foreach($collection as $item)
        {
            $occurrences[$item->getId()] = Mage::helper('Mpm/Category')->getCategoryFullPathName($item);
        }

        return $occurrences;
    }

    protected function _getProductIds($categoryId)
    {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        return Mage::getModel('catalog/product')->getCollection()->addCategoryFilter($category)->getAllIds();
    }

}