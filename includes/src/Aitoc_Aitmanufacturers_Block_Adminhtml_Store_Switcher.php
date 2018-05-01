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

class Aitoc_Aitmanufacturers_Block_Adminhtml_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher //Mage_Adminhtml_Block_Template
{
    protected $_storeIds;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('aitmanufacturers/store_switcher.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(true);
        $this->setDefaultStoreName($this->__('All Store Views'));
    }
    
   
    public function getStores($group)
    {
        $attributeCode = $this->getRequest()->get('attributecode');
        if (!$group instanceof Mage_Core_Model_Store_Group) {
            $group = Mage::app()->getGroup($group);
        }
        $stores = $group->getStores();
        
        if ($storeIds = $this->getStoreIds()) {
            foreach ($stores as $storeId => $store) {
                if (!in_array($storeId, $storeIds)) {
                    unset($stores[$storeId]);
                }
             }
        }
        
        foreach ($stores as $storeId => $store) {

            if (!Mage::helper('aitmanufacturers')->getConfigParam('is_active', $attributeCode, $storeId))
            {
                unset($stores[$storeId]);
            }
         }
    
        
        return $stores;
    }
    
    public function getStoreCollection($group)
    {
        return $this->getStores($group);
    }
}