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
* @copyright  Copyright (c) 2010 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract
{
    
    private $_attributeCode;
    private $_attributeList = array();
    
    public function _construct()
    {    
        $this->_init('aitmanufacturers/aitmanufacturers_config', 'id');
    }
    
    public function loadConfig($attributeCode, $scope = 'default', $id = 0)
    {
        $select = $this->_getReadAdapter()->select()
                    ->from($this->getMainTable())
                    ->where('attribute_code = ?', $attributeCode)
                    ->where('scope = ?', $scope)
                    ->where('scope_id = ?', $id);
                
        return $this->_getReadAdapter()->fetchRow($select);
    }
    
    public function getAttributeList()
    {
        if (!$this->_attributeList)
        {
            $select = $this->_getReadAdapter()->select()->distinct(true)
                           ->from(array('ac' => $this->getMainTable()), 'ac.attribute_code')
                           ->joinInner(array('a' => $this->getTable('eav/attribute')), 'a.attribute_code = ac.attribute_code', array('a.frontend_label'))
                           ->where('ac.is_active = 1');
            $values = $this->_getReadAdapter()->fetchAll($select);
            foreach ($values as $arr)
            {
                $this->_attributeList[$arr['attribute_code']] = $arr['frontend_label'];
            }
        }
        return $this->_attributeList;
    }
    
    public function disableBrandPages($storeId, $attributeCode)
    {
        $attributeId = Mage::getModel('aitmanufacturers/config')->getAttributeId($attributeCode);
        $optionResource = Mage::getResourceModel('eav/entity_attribute_option');
        $select = $optionResource->_getReadAdapter()->select()->from($optionResource->getMainTable(), array('option_id'))->where('attribute_id = ?', $attributeId);
        $manufacturerIds = $optionResource->_getReadAdapter()->fetchCol($select);
        
        if ($manufacturerIds)
        {
            $select = $this->_getReadAdapter()->select()
                       ->from(array('ait' => $this->getTable('aitmanufacturers/aitmanufacturers')), 'ait.id')
                       ->joinInner(array('ast' => $this->getTable('aitmanufacturers/aitmanufacturers_stores')), 'ait.id = ast.id')
                       ->where('ast.store_id = ?', $storeId)
                       ->where('ait.manufacturer_id IN (?)', $manufacturerIds);
            
            $pageIds = $this->_getReadAdapter()->fetchCol($select);
            
            if ($pageIds)
            {
               return $update = $this->_getWriteAdapter()->update($this->getTable('aitmanufacturers/aitmanufacturers'), array('status' => '2'), array('id IN (?)' => $pageIds));
            }
        }
        return false;
    }
   
}