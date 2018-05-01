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
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Manufacturers extends Mage_Core_Block_Template
{
    private $_attributeCode;
    private $_attributeId;

    public function __construct()
    {
        parent::__construct();
        $this->_attributeId = Mage::getModel('aitmanufacturers/config')->getAttributeId(Mage::registry('shopby_attribute'));
        $this->_attributeCode = Mage::registry('shopby_attribute');
    }

    public function getItems()
    {
        $c = 0;
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getBrandCollection( $this->getAttributeId(), $this->getAttributeCode() );
        $brands_withproducts_only = Mage::helper('aitmanufacturers')->getConfigParam('show_brands_withproducts_only', $this->getAttributeCode());
        foreach ($collection as $item){
            $productsNum = $item->getProductsAmount();
            if($productsNum > 0 || !$brands_withproducts_only)
            {
                // $manufacturer = $item->getManufacturer();
                $array[$item->getLetter()]['items'][] = array('item' => $item, 'number' => $productsNum);
                if (isset($array[$item->getLetter()]['count']))
                    $array[$item->getLetter()]['count']++;
                else
                    $array[$item->getLetter()]['count'] = 1;
                $c++;
            }
        }

        if (!isset($array)){
            return array();
        }
        
        $itemsPerColumn = ceil(($c + count($array)) / Mage::helper('aitmanufacturers')->getColumnsNum($this->getAttributeCode()));        
        ksort($array);

        $col = 0;
        $c = 0;
        foreach ($array as $letter => $items){
            $a[$col][$letter]=$items['items'];
            $c += $items['count'];
            $c++;
            if ($c >= $itemsPerColumn){
                $c=0;
                $col++;
            }
        }
        return $a;
    }
    
    protected function _prepareLayout()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        
        $attributeName = Mage::getModel('aitmanufacturers/config')->getAttributeName($this->getAttributeCode());
        if ($breadcrumbs)
        {
            $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
            $breadcrumbs->addCrumb('manufacturers', array('label'=>Mage::helper('aitmanufacturers')->__('All '.$attributeName.'s')));
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle(Mage::helper('aitmanufacturers')->getPageTitle($this->getAttributeCode()));
            $head->setKeywords(Mage::helper('aitmanufacturers')->getMetaKeywords($this->getAttributeCode()));
            $head->setDescription(Mage::helper('aitmanufacturers')->getMetaDescription($this->getAttributeCode()));
        }
        
    }
    
    public function getAttributeId()
    {
        return $this->_attributeId;
    }
    public function getAttributeCode()
    {
        return $this->_attributeCode;
    }
    
    public function getFeaturedAttributeName()
    {
        if ($attributeName = Mage::getModel('aitmanufacturers/config')->getAttributeName($this->getAttributeCode()))
        {
            return $this->__('Featured '.$attributeName.'s');
        }
        return $this->__('Featured Attributes');
    }
}