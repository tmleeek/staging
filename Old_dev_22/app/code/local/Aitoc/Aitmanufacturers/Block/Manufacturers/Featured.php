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

class Aitoc_Aitmanufacturers_Block_Manufacturers_Featured extends Mage_Core_Block_Template
{
    private $_attributeId;
    
    public function getItems()
    {
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addStatusFilter()
            ->addFeaturedFilter()
            ->addSortOrder();
        if ($attributeCode = Mage::registry('shopby_attribute'))
        {
            $collection->addAttributeToFilter($attributeCode, Mage::app()->getStore()->getId());
        }
        $collection->load();
        $items = $collection->getItems();

        foreach ($items as $i => $item)
        {

            if (!($attributeId = $this->getAttributeId()))
            {
                $attributeId = $this->_attributeId = Mage::getModel('aitmanufacturers/config')->getAttributeIdByOption($item->getManufacturerId());
            }

            $productIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductsByManufacturer($item->getManufacturerId(), Mage::app()->getStore()->getId(), $attributeId);
            if ( empty($productIds) && Mage::helper('aitmanufacturers')->getConfigParam('show_brands_withproducts_only', $this->getAttributeCode()) )
            {
                unset($items[$i]);
            }
        }
        return $items;
    }
    
    public function getAttributeId()
    {
        return $this->_attributeId;
    }
    
    public function getFeaturedAttributeName()
    {
        $config = Mage::getModel('aitmanufacturers/config');
        if ($attributeName = $config->getAttributeName($config->getAttributeCodeById($this->getAttributeId())))
        {
            return $this->__('Featured '.$attributeName.'s');
        }
        return $this->__('Featured Attributes');
    }
    
}