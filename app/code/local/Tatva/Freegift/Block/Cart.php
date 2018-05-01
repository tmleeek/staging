<?php
/**
 * created : 21 aout 2009
 * Liste des produits du top des ventes
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
class Tatva_Freegift_Block_Cart extends Mage_Checkout_Block_Cart_Crosssell {


   
	public function _getProductCollection()
	{
       $collection = $this->_getProducts();

       return $collection;
	}

	/**
	 * Create unique block id for frontend
	 *
	 * @return string
	 */


    public function _getProducts()
    {

		$collection=$this->getAlsoboughtProuductCollectionForFont();
        return $collection;
    }
	public function getFrontendHash()
	{
		return md5(implode("+", $this->getCacheKeyInfo()));
	}


    public function getAlsoboughtProuductCollectionForFont()
    {

        $collection = Mage::getModel('catalog/product_link')->useAlsoBoughtLinks()
            ->getProductCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('manufecturer')
            ->addStoreFilter()
            ->setPageSize('20');
        $this->_addProductAttributesAndPrices($collection);
        
        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        /*Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);*/
        return $collection;


    }
}
