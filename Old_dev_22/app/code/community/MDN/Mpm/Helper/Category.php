<?php


class MDN_Mpm_Helper_Category extends Mage_Core_Helper_Abstract {

    static $_categories = null;

    public function getProductIds($categoryIds)
    {
        $ids = array();

        if (!is_array($categoryIds))
            $categoryIds = array($categoryIds);

        foreach($categoryIds as $categoryId)
        {
            $category = new Mage_Catalog_Model_Category();
            $category->load($categoryId);
            $ids = array_merge($ids, $collection = $category->getProductCollection()->getAllIds());
        }

        return $ids;
    }

    public function getCategoryName($categoryId)
    {
        if (self::$_categories == null)
        {
            $collection = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('name');
            self::$_categories = array();
            foreach($collection as $item)
            {
                self::$_categories[$item->getId()] = $item->getName();
            }
        }

        if (isset(self::$_categories[$categoryId]))
            return self::$_categories[$categoryId];
    }

    public function getCategoryFullPathName($item, $includeRoot = false)
    {
        $items = explode('/', $item->getPath());
        $name = array();
        for($i=1 + (!$includeRoot ? 1 : 0);$i<count($items);$i++)
        {
            $name[] = $this->getCategoryName($items[$i]);
        }
        return implode(' > ', $name);
    }
}