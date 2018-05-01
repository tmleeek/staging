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
* @author Anton Vlasenko
*/
class Aitoc_Aitmanufacturers_Model_Rewrite_CatalogModelResourceEavMysql4ProductCollection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    public function getSetIds()
    {
        $select = clone $this->getSelect();
        /* @var $select Zend_Db_Select */;
        $select->reset(Zend_Db_Select::COLUMNS);
        
        // START AITOC CODE
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        // END AITOC CODE
        
        $select->distinct(true);
        $select->columns('attribute_set_id');        
        return $this->getConnection()->fetchCol($select);
    }
}