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
* @copyright  Copyright (c) 2011 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Model_Rewrite_CatalogLayerFilterCategory extends Mage_Catalog_Model_Layer_Filter_Category
{
    /**
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Aitoc_Aitmanufacturers_Model_Rewrite_CatalogLayerFilterCategory
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $helper = Mage::helper('aitmanufacturers');
        if (version_compare(Mage::getVersion(), '1.4.1.0', '>=') && !$helper->isLNPEnabled())
        {
            $filter = (int) $request->getParam($this->getRequestVar());
            
            if (!$filter)
            {
                return $this;
            }
            $this->_categoryId = $filter;

            $category   = $this->getCategory();
            Mage::register('current_category_filter', $category, true);

            $this->_appliedCategory = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($filter);

            if ($this->_isValidCategory($this->_appliedCategory)) {
                $this->getLayer()->getProductCollection()
                    ->addCategoryFilter($this->_appliedCategory);

                $this->getLayer()->getState()->addFilter(
                    $this->_createItem($this->_appliedCategory->getName(), $filter)
                );
            }

            return $this;
        }
        
        return parent::apply($request, $filterBlock);
    }
    
    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {   
        $manufacturerId = Mage::getModel('aitmanufacturers/config')->getAttributeId(Mage::registry('shopby_attribute'));
        $manufacturers = Mage::registry('aitmanufacturers_manufacturers');
        $manufacturerValue = $manufacturers[Mage::app()->getRequest()->getParam('id')];

// Fix for Category Product Quantity (mantis 28256). Use Product Id filter, not filtering by attribute
        if($manufacturerId || $manufacturerValue)
        {
            $productIds = Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductsByManufacturer($manufacturerValue, Mage::app()->getStore()->getId(), $manufacturerId);
            $this->getLayer()->getProductCollection()
                    ->addIdFilter($productIds);
        }
        $this->getLayer()->getProductCollection()
//                ->addIdFilter($productIds)
//                ->addAttributeToFilter($manufacturerCode,$manufacturerValue)
                ->addAttributeToFilter('visibility',array(2,3,4))
                ;
        return parent::_getItemsData();
    }
}