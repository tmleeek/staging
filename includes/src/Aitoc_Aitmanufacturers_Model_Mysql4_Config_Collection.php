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

class Aitoc_Aitmanufacturers_Model_Mysql4_Config_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    
    protected $_previewFlag;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('aitmanufacturers/config');
    }
    
    public function loadByStoreId($storeId)
    {
        
        //parent::_initSelect();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $this->getSelect()->where("(scope_id = '$storeId' AND scope = 'store') 
                                OR (scope_id = '$websiteId' AND scope = 'website') 
                                OR (scope_id = '0' AND scope = 'default')")
                          ->where('is_active = true');
        return $this;
    }
    
}