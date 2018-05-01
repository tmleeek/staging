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
class Aitoc_Aitmanufacturers_Block_Rewrite_PageHtmlTopmenu extends Mage_Page_Block_Html_Topmenu
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
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getBrandCollection( $this->getAitAttributeId(), $this->getAitAttributeCode() );
        $brands_withproducts_only = Mage::helper('aitmanufacturers')->getConfigParam('show_brands_withproducts_only', $this->getAitAttributeCode());

        foreach ($collection as $item)
        {

            $productsNum = $item->getProductsAmount();
            if ($productsNum > 0 || !$brands_withproducts_only)
            {
                $array[] = array('item' => $item, 'number' => $productsNum);
            }
        }

        if (!isset($array)){
            return array();
        }
        
        return $array;
    }
    
    protected function getAitAttributeList()
    {
        return Mage::getResourceModel('aitmanufacturers/config')->getAttributeList();
    }
    
    protected function getAitActiveCategoriesCount()
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

        return $activeCategoriesCount;
    }
    
    public function getStoreCategories()
    {
        $helper = Mage::helper('catalog/category');
        return $helper->getStoreCategories();
    }
    
    public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '')
    {
        if($this->getAitAttributesCount() > 0)
        {
            $html = '';
            $lastJ = $this->getAitActiveCategoriesCount() + 1;
            
            $index = 0;
            foreach ($this->getAitAttributeList() as $code => $attribute)
            {
                if(Mage::helper('aitmanufacturers')->getIsActive($code) && Mage::helper('aitmanufacturers')->getConfigParam('include_in_navigation_menu', $code))
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
                        Mage::helper('aitmanufacturers')->getManufacturersUrl($code) . '" class="level-top"><span>' . 
                        Mage::helper('aitmanufacturers')->getAttributeName($code) . '</span></a>';
            
            
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
                            
                            if (!($brandItem = $manufacturer['item']->getStoreValue()))
                            {
                                $brandItem = $manufacturer['item']->getDefaultValue();
                            }
                            
                            $number = !is_null($manufacturer['number']) ? ' ('.$manufacturer["number"].')' : '';
                            
                            $insertedIndex = $manufIndex + 1;
                            $brandsPageButton .= '<li class="level1 nav-' . $lastJ . '-' . $insertedIndex .' ' . $manufFirstLast . ' ' . $activeSub . '">
                                <a class="" href="'.Mage::helper('aitmanufacturers')->generateUrl($manufacturer['item']->getData('url_key'), $manufacturer['item']->getData('manufacturer_id')).'">
                                <span>' . $brandItem . $number.'</span></a></li>';
                    
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
    }
    
    //overwrite parent
    public function getHtml($outermostClass = '', $childrenWrapClass = '')
    {
        $html = parent::getHtml($outermostClass, $childrenWrapClass);
        $html .= $this->renderCategoriesMenuHtml();
        return $html;
    }
}