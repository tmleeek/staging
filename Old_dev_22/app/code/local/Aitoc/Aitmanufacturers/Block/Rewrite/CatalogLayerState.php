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
class Aitoc_Aitmanufacturers_Block_Rewrite_CatalogLayerState extends Mage_Catalog_Block_Layer_State
{
    protected $_filterBlocks = null;    
    
    public function getActiveFilters()
    {
        if (is_null($this->_filterBlocks))
        {
            $this->_filterBlocks = parent::getActiveFilters();
            Mage::dispatchEvent('aitoc_aitmanufacturers_layer_state_filters_get_after', array('layer_state_block' => $this));
        }
        
        return $this->_filterBlocks;
    }
    
    /**
     *
     * @param string $name
     * @return Aitoc_Aitmanufacturers_Block_Rewrite_CatalogLayerState 
     */
    public function unsetActiveFilter($name)
    {
        unset($this->_filterBlocks[$name]);
        return $this;
    }
}