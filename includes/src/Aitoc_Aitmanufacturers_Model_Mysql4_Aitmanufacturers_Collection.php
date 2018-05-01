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

class Aitoc_Aitmanufacturers_Model_Mysql4_Aitmanufacturers_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    
    protected $_previewFlag;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitmanufacturers/aitmanufacturers');
    }
    
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $sql = $this->getSelectCountSql();
            if(strpos($sql,'GROUP BY')!==false)
            {
                $this->_totalRecords = count($this->getConnection()->fetchAll($sql, $this->_bindParams));
            }
            else
            {
                $this->_totalRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
            }
        }
        return intval($this->_totalRecords);
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $storeId = Mage::app()->getStore()->getId();
        $this->_optionValueTable = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value');
        $this->getSelect()
            ->join(array('store_default_value'=>$this->_optionValueTable),
                'store_default_value.option_id=main_table.manufacturer_id',
                array('default_value'=>'value'))
            ->joinLeft(array('store_value'=>$this->_optionValueTable),
                'store_value.option_id=main_table.manufacturer_id AND '.$this->getConnection()->quoteInto('store_value.store_id=?', $storeId),
                array('store_value'=>'value',
                	  'manufacturer' => new Zend_Db_Expr('IFNULL(store_value.value,store_default_value.value)'),
                      'letter' => new Zend_Db_Expr('UPPER(LEFT(IFNULL(store_value.value,store_default_value.value), 1))'),
                ))
            ->where($this->getConnection()->quoteInto('store_default_value.store_id=?', 0));
            //->order('manufacturer');
        return $this;
    }
    
    protected function _afterLoad()
    {
        if ($this->_previewFlag) {
            $items = $this->getColumnValues('id');
            if (count($items)) {
                $select = $this->getConnection()->select()
                        ->from($this->getTable('aitmanufacturers/aitmanufacturers_stores'))
                        ->where($this->getTable('aitmanufacturers/aitmanufacturers_stores').'.manufacturers_id IN (?)', $items);
                if ($result = $this->getConnection()->fetchPairs($select)) {
                    foreach ($this as $item) {
                        if (!isset($result[$item->getData('manufacturers_id')])) {
                            continue;
                        }
                        if ($result[$item->getData('manufacturers_id')] == 0) {
                            $stores = Mage::app()->getStores(false, true);
                            $storeId = current($stores)->getId();
                            $storeCode = key($stores);
                        } else {
                            $storeId = $result[$item->getData('manufacturers_id')];
                            $storeCode = Mage::app()->getStore($storeId)->getCode();
                        }
                        $item->setData('_first_store_id', $storeId);
                        $item->setData('store_code', $storeCode);
                    }
                }
            }
        }

        parent::_afterLoad();
    }
    
    
    /* @fix this too */
    /**
     * Add Filter by store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return Mage_Cms_Model_Mysql4_Page_Collection
     */
    public function addStoreFilter($store, $strict = false)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = $store->getId();
        }
        /*if (!$strict && Mage::helper('aitmanufacturers')->getAttributeCode(0) != Mage::helper('aitmanufacturers')->getAttributeCode($store)){
            $strict = true;
        }*/
        $stores = array();
        if (!$strict){
            $stores = array(0);
            $select = $this->getConnection()->select()
                ->from(array('stores_table'=>$this->getTable('aitmanufacturers/aitmanufacturers_stores')))
                ->where('stores_table.store_id IN (?)', array_merge($stores, (array)$store))
                ->order('stores_table.store_id DESC')
                ->order('stores_table.manufacturer_id');
            $ids = $this->getConnection()->fetchPairs($select);
            $ids = array_keys(array_unique($ids));
            $this->getSelect()->where('main_table.id in (?)', $ids);
        }
        else {
            $this->getSelect()->join(
                array('stores_table' => $this->getTable('aitmanufacturers/aitmanufacturers_stores')),
                'main_table.id = stores_table.id',
                array('store_id')
            )
            ->where('stores_table.store_id in (?)', array_merge($stores, (array)$store));
        }
        return $this;
    }
    
    /*public function load($printQuery = false, $logQuery = false)
    {
        print_r($this->getSelectSql(true));exit;
    }*/
    
    public function addSortOrder()
    {
        $this->getSelect()->columns(
            array('_sort' => 'IF(main_table.sort_order > 0, main_table.sort_order, 999999999)')
        )
        ->reset('order')
        ->order('_sort')
        ->order('manufacturer');
        
        return $this;
    }
    
    public function addFieldToFilter($field, $cond=null)
    {
        if ('manufacturer' == $field && $cond){
            $field = new Zend_Db_Expr('IFNULL(store_value.value,store_default_value.value)');
        }
        elseif ('id' == $field)
        {
            $field = 'main_table.id';
        }
        return parent::addFieldToFilter($field, $cond);
    }
    
    public function addAttributeToFilter($attributeCode, $scopeId)
    {
        
        $ids = array();
        $collection = $this->getManufacturersCollection(array(), $scopeId, $attributeCode);
        
        foreach ($collection as $item)
        {
            $ids[] = $item->getId();
        }
        $this->addFieldToFilter('main_table.manufacturer_id', array('in' => $ids));
        
        return $this;
    }
    
    protected function _getManufacturersCollection($storeId=null, $attributeCode)
    {
        return Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter(Mage::helper('aitmanufacturers')->getAttributeId($storeId, $attributeCode))
                ->setStoreFilter(is_null($storeId)?Mage::app()->getStore():$storeId, true)
                ->setPositionOrder('desc', true);
    }
    
    public function getManufacturersCollection($filderIds = array(), $storeId = 0, $attributeCode)
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter(Mage::helper('aitmanufacturers')->getAttributeId($storeId, $attributeCode))
                ->setStoreFilter($storeId);
        if (!empty($filderIds)){
            $collection->getSelect()->where('main_table.option_id NOT IN (?)', $filderIds);
        }
        return $collection->load();
    }
    
    public function toManufacturersOptionsArray($storeId=null, $attributeCode)
    {
        $collection = $this->_getManufacturersCollection($storeId, $attributeCode);
        //$collection->getSelect()->where('main_table.option_id NOT IN (?)', $this->getColumnValues('manufacturer_id'));
        return $collection->load()->toOptionArray();
    }
    
    public function addStatusFilter()
    {
        $this->getSelect()->where('main_table.status = 1');
        return $this;
    }
    
    public function addFeaturedFilter()
    {
        $this->getSelect()->where('main_table.featured = 1');
        return $this;
    }
    
    public function addLimit($limit = null)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }
    
}