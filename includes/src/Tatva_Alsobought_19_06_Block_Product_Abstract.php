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
class Tatva_Alsobought_Block_Product_Abstract extends Mage_Catalog_Block_Product_List {
	/**
	 * Retourne une collection de produits à afficher dans le bloc "les internautes ont également acheté"
	 * Prend en compte le produit courant
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	protected function _getProducts() {
		$product = $this->_getCurrentProduct();
		$collection = $product->getAlsoBoughtProductCollection();
		$collection->addAttributeToFilter('status', 1)
			->setPageSize(Mage::getStoreConfig('alsobought/general/limit'));
		
		
		
		$attributes = Mage::getSingleton('catalog/config')
            ->getProductAttributes();
        $collection->addAttributeToSelect($attributes)
        	->addAttributeToSelect('manufacturer')
        	->addAttributeToSelect('gamme_collection_new')
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents();
		
		// Tri aléatoire
		$collection->getSelect()->order('rand()');
		
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		
		return $collection;
	}
	
	protected function _getCurrentProduct() {
		return Mage::registry('product');
	}
}
