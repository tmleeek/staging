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
class Aitoc_Aitmanufacturers_Block_Rewrite_CatalogLayerView extends Mage_Catalog_Block_Layer_View
{
    protected $_filterBlocks = null;
    
    /**
     *
     * @return array
     */
    public function getFilters()
    {
        if (is_null($this->_filterBlocks))
        {
            $this->_filterBlocks = parent::getFilters();
            Mage::dispatchEvent('aitoc_aitmanufacturers_layer_filters_get_after', array('layer_view_block' => $this));
        }
        
        return $this->_filterBlocks;
    }

    /**
     *
     * @param string $name
     * @return Aitoc_Aitmanufacturers_Block_Rewrite_CatalogLayerView 
     */
    public function unsetFilter($name)
    {
        unset($this->_filterBlocks[$name]);
        return $this;
    }
}
?>