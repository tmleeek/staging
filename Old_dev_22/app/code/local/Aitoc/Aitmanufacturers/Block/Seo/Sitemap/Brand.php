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
class Aitoc_Aitmanufacturers_Block_Seo_Sitemap_Brand extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{

    /**
     * Initialize products collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        /* !AITOC_MARK:manufacturer_collection_index */
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
        $collection->addStoreFilter($storeId)->addStatusFilter()->addSortOrder();
   //     if (Mage::getStoreConfig('catalog/aitmanufacturers/manufacturers_show_brands_withproducts_only'))
        //{
                $collection->getSelect()->joinInner(
                        array('PRD' => Mage::getResourceModel('catalogindex/attribute')->getMainTable()),
                                'PRD.value = main_table.manufacturer_id'
                )->group('main_table.manufacturer_id');
        //}
        $this->setCollection($collection);
        return $this;
    }
    
    public function getDisplayCollection()
    {
        /* !AITOC_MARK:manufacturer_collection_index */
    	$storeId = Mage::app()->getStore()->getId();
		
		$attributes = Mage::getModel('aitmanufacturers/config')->getResource()->getAttributeList();
		$attributesCollection = array();
		
		foreach ($attributes as $code => $title)
		{
		    if (Mage::helper('aitmanufacturers')->getConfigParam('show_brands_in_sitemap', $code, $storeId) && Mage::helper('aitmanufacturers')->getConfigParam('is_active', $code, $storeId))
		    {
		        $options = Mage::getModel('eav/entity_attribute_option')->getCollection()
                        ->addFieldToFilter('attribute_id', array('eq' => Mage::getModel('aitmanufacturers/config')->getAttributeId($code)))->toOptionArray();
                $ids = array();
                foreach ($options as $item)
                {
                    $ids[] = $item['value'];
                }
        
		        $collection = Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();		
		        $collection->addStoreFilter($storeId)->addStatusFilter()->addSortOrder()
        		           ->addFieldToFilter('main_table.manufacturer_id', array('in' => $ids));
        		 	
        		if (Mage::helper('aitmanufacturers')->getConfigParam('show_brands_withproducts_only', $code, $storeId) && Mage::helper('aitmanufacturers')->getConfigParam('is_active', $code, $storeId))
        		{
        			$collection->getSelect()->joinInner(
        				array('PRD' => Mage::getResourceModel('catalogindex/attribute')->getMainTable()),
        					  'PRD.value = main_table.manufacturer_id'
                    	)->group('main_table.manufacturer_id');
        		}
        		
        		if ($collection->getSize())
        		{
        		    $attributesCollection[$title] = $collection;
        		}
		    }
		    
		}
		return $attributesCollection;
		//return $collection;
    }
    
    public function getItemUrl($item)
    {
    	$item->getUrl();
    }    
}