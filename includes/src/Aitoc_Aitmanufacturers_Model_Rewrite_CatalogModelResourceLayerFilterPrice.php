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

class Aitoc_Aitmanufacturers_Model_Rewrite_CatalogModelResourceLayerFilterPrice extends Mage_Catalog_Model_Resource_Layer_Filter_Price
{
     /**
     * Retrieve clean select with joined price index table
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return Varien_Db_Select
     */
    protected function _getSelect($filter)
    {
        if(version_compare(Mage::getVersion(),'1.7.0.1','<')) {
            return parent::_getSelect($filter);
        }
        $collection = $filter->getLayer()->getProductCollection();
        $collection->addPriceData($filter->getCustomerGroupId(), $filter->getWebsiteId());

        if (!is_null($collection->getCatalogPreparedSelect())) {
            $select = clone $collection->getCatalogPreparedSelect();
        } else {
            $select = clone $collection->getSelect();
        }

        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        // remove join with main table
        $fromPart = $select->getPart(Zend_Db_Select::FROM);
        if (!isset($fromPart[Mage_Catalog_Model_Resource_Product_Collection::INDEX_TABLE_ALIAS])
            || !isset($fromPart[Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS])
        ) {
            return $select;
        }
        // processing FROM part
        $priceIndexJoinPart = $fromPart[Mage_Catalog_Model_Resource_Product_Collection::INDEX_TABLE_ALIAS];
        $priceIndexJoinConditions = explode('AND', $priceIndexJoinPart['joinCondition']);
        $priceIndexJoinPart['joinType'] = Zend_Db_Select::FROM;
        $priceIndexJoinPart['joinCondition'] = null;
        $fromPart[Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS] = $priceIndexJoinPart;
        unset($fromPart[Mage_Catalog_Model_Resource_Product_Collection::INDEX_TABLE_ALIAS]);
        $select->setPart(Zend_Db_Select::FROM, $fromPart);

        foreach ($fromPart as $key => $fromJoinItem) {
            $fromPart[$key]['joinCondition'] = $this->_replaceTableAlias($fromJoinItem['joinCondition']);
        }
        $select->setPart(Zend_Db_Select::FROM, $fromPart);

        // processing WHERE part
        $wherePart = $select->getPart(Zend_Db_Select::WHERE);
        $useVisibilityFilter = false;
        foreach ($wherePart as $key => $wherePartItem) {
            $wherePart[$key] = $this->_replaceTableAlias($wherePartItem);
            if ( false !== strpos($wherePartItem,'visibility') )
            {
                $useVisibilityFilter = true;
                $wherePart[$key] = $this->_replaceVisibilityAlias($wherePartItem);
                
            }
        }
        $select->setPart(Zend_Db_Select::WHERE, $wherePart);
        
        $excludeJoinPart = Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS . '.entity_id';
        foreach ($priceIndexJoinConditions as $condition) {
            if (strpos($condition, $excludeJoinPart) !== false) {
                continue;
            }
            $select->where($this->_replaceTableAlias($condition));
        }
        $select->where($this->_getPriceExpression($filter, $select) . ' IS NOT NULL');
        

        if ($useVisibilityFilter)
        {
            $currentCategory = Mage::registry('current_category_filter');
            if ($currentCategory)
            {
                $currentCategory = $currentCategory->getId();
            } else {
                $currentCategory = Mage::app()->getStore()->getRootCategoryId();
            }
            
            $select->join(
                    array('ev' =>  $this->getTable('catalog/category_product_index')),
                    Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS.'.entity_id = ev.product_id AND ev.store_id = '.Mage::app()->getStore()->getId() .' AND ev.category_id = '.$currentCategory,
                    array()
                );
                
        }
        
        return $select;
    }
    
    protected function _replaceVisibilityAlias($conditionString)
    {
        $adapter = $this->_getReadAdapter();
        $oldAlias = array(
            Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS . '.',
            $adapter->quoteIdentifier(Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS) . '.',
        );
        $newAlias = array(
            'ev.',
            $adapter->quoteIdentifier('ev') . '.',
        );
        
        return str_replace($oldAlias, $newAlias, $conditionString);
    }
    
}