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
* @todo in the future...
*/

class Aitoc_Aitmanufacturers_Block_Manufacturers_Brief extends Mage_Core_Block_Template
{
    private $_attributeCode = null;
    
    public function setAttributeCode($code)
    {
        $this->_attributeCode = $code;
        return $this;
    }
    
    public function getAttributeCode()
    {
        return $this->_attributeCode;
    }
    
    public function getItems()
    {
        if (Mage::helper('aitmanufacturers')->getConfigParam('show_brands_from_category_only', $this->getAttributeCode()) && !is_null(Mage::registry('current_category')))
        {
            return $this->getCategoryItems();
        }
        
        if (!Mage::helper('aitmanufacturers')->getBriefNum($this->getAttributeCode()))
        {
            return array();
        }
        
        if (Mage::helper('aitmanufacturers')->getConfigParam('show_brands_withproducts_only', $this->getAttributeCode()))
        {
            $items = array();
            $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addAttributeToFilter($this->getAttributeCode(),Mage::app()->getStore()->getId())
                ->addStatusFilter()
                ->addSortOrder();
            $this->_totalNum = $collection->count();
            $collection->clear();
            $collection->load();
            $attributeId = Mage::getModel('aitmanufacturers/config')->getAttributeId($this->getAttributeCode());
            
            foreach ($collection as $item)
            {
                $productIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductsByManufacturer($item->getManufacturerId(), Mage::app()->getStore()->getId(), $attributeId);
                if (!empty($productIds))
                {
                    $items[] = $item;
                }
                if (count($items) >= Mage::helper('aitmanufacturers')->getBriefNum($this->getAttributeCode()))
                {
                    return $items;
                }
            }
            
            return $items;
        } 
        else 
        {
            $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addAttributeToFilter($this->getAttributeCode(),Mage::app()->getStore()->getId())
                ->addStatusFilter()
                ->addSortOrder();
            $this->_totalNum = $collection->count();
            $collection->clear();
            
            $collection->addLimit(Mage::helper('aitmanufacturers')->getBriefNum($this->getAttributeCode()));
            $collection->load();
            return $collection->getItems();
        }
    }
    
    public function getCategoryItems()
    {
        /* !AITOC_MARK:manufacturer_collection */
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        $productCollection->addCategoryFilter(Mage::registry('current_category'))->load();
        $productIds = array();
        foreach ($productCollection as $product)
        {
            $productIds[] = $product->getId();
        }
        
        $manufacturerIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getManufacturersByProducts($productIds, Mage::app()->getStore()->getId(), Mage::getModel('aitmanufacturers/config')->getAttributeId($this->getAttributeCode()));
        
        if (empty($manufacturerIds))
        {
            return array();
        }
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
                                                                         ->addStoreFilter(Mage::app()->getStore()->getId())
                                                                         ->addStatusFilter()
                                                                         ->addSortOrder()
                                                                         ->addFieldToFilter('main_table.manufacturer_id', array('in' => $manufacturerIds));
        $this->_totalNum = $collection->count();
        $collection->clear();
        $collection->addLimit(Mage::helper('aitmanufacturers')->getBriefNum($this->getAttributeCode()));
        $collection->load();
        return $collection->getItems();
    }
    
    public function getAttributeList()
    {
        return Mage::getResourceModel('aitmanufacturers/config')->getAttributeList();
    }
    
    public function getTotal()
    {
        return $this->_totalNum;
    }
}