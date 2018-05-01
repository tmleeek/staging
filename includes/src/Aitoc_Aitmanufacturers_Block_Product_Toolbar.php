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
class Aitoc_Aitmanufacturers_Block_Product_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    /**
     * Remove order from available orders if exists
     *
     * @param string $order
     * @param Mage_Catalog_Block_Product_List_Toolbar
     */
    public function removeOrderFromAvailableOrders($order)
    {
        if (isset($this->_availableOrder[$order])) {
            unset($this->_availableOrder[$order]);
        }
        return $this;
    }
    
    public function setAvailableOrders($orders)
    {
        $helper = Mage::helper('aitmanufacturers');
        if ($helper->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true))
        {
            if (isset($orders['aitmanufacturers_sort']) && isset($orders['position']))
            {
                unset($orders['position']);
            }
        }
        if(Mage::app()->getRequest()->getModuleName() != 'brands')
        {
            if(!$helper->canUseLayeredNavigation(Mage::registry('shopby_attribute'), true))
                unset($orders['aitmanufacturers_sort']);
        }
        $this->_availableOrder = $orders;
        return $this;
    }
    
    public function _construct()
    { 
        if ('aitmanufacturers_index_view' == Mage::app()->getFrontController()->getFullActionName())
        {
            $this->setDefaultDirection('asc');
            $this->setDefaultOrder('position');            
        }
        parent::_construct();
    }
    
    public function getDefaultOrder()
    {
        return $this->_orderField;
    }
    
}