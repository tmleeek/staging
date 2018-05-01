<?php
/**
 * created : 21 aout 2009
 * Alsobought product controller
 * 
 * 
 * @category SQLI
 * @package Sqli_Alsobought
 * @author sgautier
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Alsobought
 */
class Tatva_Catalog_Model_Product_Link extends Mage_Catalog_Model_Product_Link
{
    const LINK_TYPE_ALSOBOUGHT   = 100;

    /**
     * @return Mage_Catalog_Model_Product_Link
     */
    public function useAlsoBoughtLinks()
    {  
        $this->setLinkTypeId(self::LINK_TYPE_ALSOBOUGHT);
        return $this;
    }

    /**
     * Save data for product relations
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_Model_Product_Link
     */
    public function saveProductRelations($product)
    {
        $data = $product->getAlsoBoughtLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductLinks($product, $data, self::LINK_TYPE_ALSOBOUGHT);
        }
        return parent::saveProductRelations($product);
    }
}