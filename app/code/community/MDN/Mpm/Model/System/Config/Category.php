<?php

class MDN_Mpm_Model_System_Config_Category extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    /**
     *
     * @return type
     */
    public function getAllOptions() {

        if (!$this->_options) {
            $this->_options = array();

            $this->getCategoryTree($this->_options, 0, 0);

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

    protected function getCategoryTree(&$array, $level, $parentId)
    {
        $categories = Mage::getModel('catalog/category')->getCollection()->addAttributeToSelect('name')->addFieldToFilter('parent_id', $parentId);
        foreach($categories as $category)
        {
            $indent = str_repeat('-', $level * 4);
            $array[$category->getId()] = '|'.$indent.' '.$category->getName();
            $this->getCategoryTree($array, $level + 1, $category->getId());
        }
    }

}
