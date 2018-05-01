<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

class MageWorx_SearchAutocomplete_Block_Autocomplete extends Mage_Catalog_Block_Product {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('searchautocomplete/autocomplete.phtml');
    }

    protected function sanitizeContent($page) {        
        return Mage::helper('searchautocomplete')->sanitizeContent($page);
    }

    protected function getProductsGroupedByCategories() {
        $groupedProducts = $this->explodeProductsByCategory($this->getProducts());
        usort($groupedProducts, array('MageWorx_SearchAutocomplete_Block_Autocomplete', 'cmpCategories'));
        foreach ($groupedProducts as $key => $group) {
            $groupedProducts[$key]['products'] = $this->_sortProducts($group['products']);
        }
        return $groupedProducts;
    }
    /**
     *  Explode Products By Category (Priority - low level category)
     * 
     * @param type $products
     * @return array Exploded Products By Category 
     */
    protected function explodeProductsByCategory($products) {
        $categoriesWithGroupedProducts = array();
        foreach ($products as $product) {
            $categoryIds = $product->getCategoryIds();
            $categories = array();
            foreach ($categoryIds as $categoryId) {
                $cat = Mage::getModel('catalog/category')->load($categoryId);
                if ($cat) {
                    array_push($categories, $cat);
                }
            }
            $categories = $this->filterCategoriesByLevel($categories);
            foreach ($categories as $category) {
                $id = $category->getId();
                if (!isset($categoriesWithGroupedProducts[$id])) {
                    $categoriesWithGroupedProducts[$id] = array('category' => $category, 'products' => array($product));
                } else {
                    $categoriesWithGroupedProducts[$id]['products'][] = $product;
                }
            }
        }
        return $categoriesWithGroupedProducts;
    }
    /**
     *  Filter Categories By Low Level
     * 
     * @param type $categories
     * @return array Filtered Categories
     */
    protected function filterCategoriesByLevel($categories) {
        $low = 0;
        $filtered = array();
        foreach ($categories as $category) {
            $level = $category->getLevel();
            if ($level > $low)
                $low = $level;
        }
        foreach ($categories as $category) {
            $level = $category->getLevel();
            if ($level == $low) {
                array_push($filtered, $category);
            }
        }
        return $filtered;
    }
    

    protected function getSortProducts() {
        return $this->_sortProducts($this->getProducts());
    }
    
    /**
     * @return $_products
     */
    protected function _sortProducts($products) {
        $sortOrder = Mage::helper('searchautocomplete')->getProductSearchResultsSortOrder();

        if (is_object($products)) {
            $p = array();
            foreach ($products as $product) {
                array_push($p, $product);
            }
            $products = $p;
        }
        if ($sortOrder == 'name_asc') {
            usort($products, array('MageWorx_SearchAutocomplete_Block_Autocomplete', 'cmpNames'));
        } elseif ($sortOrder == 'name_desc') {
            usort($products, array('MageWorx_SearchAutocomplete_Block_Autocomplete', 'cmpNames'));
            $products = array_reverse($products);
        } elseif ($sortOrder == 'price_desc') {
            usort($products, array('MageWorx_SearchAutocomplete_Block_Autocomplete', 'cmpPrices'));
        } elseif ($sortOrder == 'price_asc') {
            usort($products, array('MageWorx_SearchAutocomplete_Block_Autocomplete', 'cmpPrices'));
            $products = array_reverse($products);
        }
        return $products;
    }
    /**
     *  Callback function. Compare Names Products
     * 
     * @param type $a 
     * @param type $b
     * @return type int
     */
    public static function cmpNames($a, $b) {
        return strcmp($a->getName(), $b->getName());
    }
    /**
     *  Callback function. Compare Prices Products
     * 
     * @param type $a
     * @param type $b
     * @return type int
     */
    public static function cmpPrices($a, $b) {
        $price1 = $a->getFinalPrice();
        $price2 = $b->getFinalPrice();
        if ($price1 > $price2)
            return -1;
        elseif ($price1 == $price2)
            return 0;
        else
            return 1;
    }
    /**
     *  Callback function. Compare Categories
     * 
     * @param type $a
     * @param type $b
     * @return type int
     */
    public static function cmpCategories($a, $b) {
        $countproducts1 = count($a['products']);
        $countproducts2 = count($b['products']);
        if ($countproducts1 > $countproducts2)
            return -1;
        elseif ($countproducts1 == $countproducts2)
            return 0;
        else
            return 1;
    }
    
   
    /**
     * Retrieve attribute instance by name, id or config node
     *
     * If attribute is not found false is returned
     *
     * @param string|integer|Mage_Core_Model_Config_Element $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Abstract || false
     */
    public function getProductAttribute($attribute) // for 1.13.0.0
    {
        return $this->getProduct()->getResource()->getAttribute($attribute);
    }
    
}
