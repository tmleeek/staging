<?php

class Tatva_Freegift_Model_Mysql4_Freegift_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('freegift/freegift');
    }
	
	 /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store Store to be filtered
     * @return Flagbit_Faq_Model_Mysql4_Faq_Collection Self
     */
    public function addStoreFilter($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array (
                 $store->getId()
            );
        }
        
        $this->getSelect()->where('store_id in (?)', array (0, $store));
        
        return $this;
    }
}