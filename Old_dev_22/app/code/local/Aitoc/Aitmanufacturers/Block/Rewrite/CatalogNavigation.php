<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
class Aitoc_Aitmanufacturers_Block_Rewrite_CatalogNavigation extends Mage_Catalog_Block_Navigation
{
    private $_aitAttributeCode;
    private $_aitAttributeId;
    private $_aitNumberOfAttributes = 0;
    
    protected function setAitAttributeId($code)
    {
        $this->_aitAttributeId = Mage::getModel('aitmanufacturers/config')->getAttributeId($code);
    }
    
    protected function getAitAttributeId()
    {
        return $this->_aitAttributeId;
    }
    
    protected function setAitAttributeCode($code)
    {
        $this->_aitAttributeCode = $code;
    }
    
    protected function getAitAttributeCode()
    {
        return $this->_aitAttributeCode;
    }
    
    protected function getAitAttributesCount()
    {
        if ( empty($this->_aitNumberOfAttributes) )
        {
            $this->_aitNumberOfAttributes = count(Mage::helper('aitmanufacturers/manufacturer')->getActiveAttributes());
        }
        return $this->_aitNumberOfAttributes;
    }
    
    protected function getAitItems()
    {
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getBrandCollection($this->getAitAttributeId(), $this->getAitAttributeCode());
        $brands_withproducts_only = Mage::helper('aitmanufacturers')->getConfigParam('show_brands_withproducts_only', $this->getAttributeCode());

        foreach ($collection as $item)
        {
            $productsNum = $item->getProductsAmount();
            if($productsNum > 0 || !$brands_withproducts_only)
            {
                $array[] = array('item' => $item, 'number' => $productsNum);
            }
        }

        if (!isset($array))
        {
            return array();
        }
        
        return $array;
    }
    
    protected function getAitAttributeList()
    {
        return Mage::helper('aitmanufacturers/manufacturer')->getAttributes();
    }
    
    protected function getCategoriesMenuData($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) 
        {
            if ($child->getIsActive()) 
            {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) 
        {
            return array();
        }

        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) 
        {
            $html .= $this->_renderCategoryMenuItemHtml(
                    $category,
                    $level,
                    ($j == $activeCategoriesCount),
                    ($j == 0),
                    true,
                    $outermostItemClass,
                    $childrenWrapClass,
                    true
                );
            $j++;
        }
        
        return array('html' => $html, 'lastIndex' => $j);
    }
    
    //overwrite parent
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        if( $this->getAitAttributesCount() > 0 )
        {
            $data = $this->getCategoriesMenuData($level, $outermostItemClass, $childrenWrapClass);
            $html = $data['html'];
            $lastJ = $data['lastIndex'] + 1;
            $helper = Mage::helper('aitmanufacturers');
            
            $index = 0;
            foreach ($this->getAitAttributeList() as $code => $attribute)
            {
                if($helper->getIsActive($code) && $helper->getConfigParam('include_in_navigation_menu', $code))
                {
                    $this->setAitAttributeCode($code);
                    $this->setAitAttributeId($code);
                
                    $manufacturers = $this->getAitItems();
                
                    $last = '';
                    $parent = '';
                    
                    if($this->_aitNumberOfAttributes-1 == $index)
                    {
                        $last = 'last';
                    }
                
                    if (count($manufacturers))
                    {
                        $parent = 'parent';
                    }
                    
                    $activeMain = Mage::helper('aitmanufacturers')->isActiveMenuMain($code) ||
                                  Mage::helper('aitmanufacturers')->isActiveMenuSub($code, $manufacturers) ? 'active' : '';

                    $brandsPageButton = '<li class="level0 nav-' . $lastJ . ' level-top ' . $last . ' ' . $parent . ' ' . $activeMain . '"><a href="' . 
                    $helper->getManufacturersUrl($code) . '" class="level-top"><span>' . 
                    $helper->__($helper->getAttributeName($code)) . '</span></a>';
            
                    if (count($manufacturers))
                    {
                        $brandsPageButton .= '<ul class="level0">';
                        $manufIndex = 0;
                        foreach ($manufacturers as $manufacturer)
                        {
                            $manufFirstLast = '';
                            if($manufIndex === 0)
                            {
                                $manufFirstLast = 'first';
                            }
                            elseif($manufIndex === count($manufacturers) - 1)
                            {
                                $manufFirstLast = 'last';
                            }
                            
                            $activeSub = Mage::helper('aitmanufacturers')->isActiveMenuSub($code, $manufacturers, $manufacturer['item']->getManufacturerId()) ? 'active' : '';
                    
                            $insertedIndex = $manufIndex + 1;
                            $brandsPageButton .= '<li class="level1 nav-' . $lastJ . '-' . $insertedIndex .' ' . $manufFirstLast . ' ' . $activeSub . '">
                                <a class="" href="' . $manufacturer['item']->getUrl() . '">
                                <span>' . $manufacturer['item']->getManufacturerName($manufacturer['item']->getManufacturerId()).'</span></a></li>';
                    
                            $manufIndex ++;
                        }
                        $brandsPageButton .= '</ul>';
                    }
            
                    $brandsPageButton . '</li>';
                            
                    $html .= $brandsPageButton;
                    $lastJ ++;
                    $index ++;
                }
            }
        
            return $html;
        }
        else
        {
            return parent::renderCategoriesMenuHtml($level, $outermostItemClass, $childrenWrapClass);
        }
    }
}