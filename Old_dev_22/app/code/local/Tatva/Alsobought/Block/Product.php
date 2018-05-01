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
class Tatva_Alsobought_Block_Product extends Tatva_Alsobought_Block_Product_Abstract {
	
	const CACHE_TAG = "TATVA_ALSOBOUGHT_PRODUCT";
	
	public function __construct() {
		
		parent::__construct();

		if (Mage::getStoreConfig('tatvacaching/ttl/active')==1 ) {
			if($this->_getCurrentProduct()){
				
				$product = $this->_getCurrentProduct();
				$this->addData(array(
		            'cache_lifetime'    => Mage::getStoreConfig('tatvacaching/ttl/medium'),
		        	'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG . "_" . $product->getId () ),
		        	'cache_key' 		=> self::CACHE_TAG . '_'. $product->getId()  . '_'. Mage::app()->getStore()->getId(),  
	        	));
			}		
		}
	}
	
	
	
	/**
	 * Retourne une collection de produits à afficher dans le bloc "les internautes ont également acheté"
	 * Prend en compte le produit courant
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	public function getProducts() {
		$collection = $this->_getProducts()
			->addAttributeToSelect('name')
			->addAttributeToSelect('description');
		$collection->load();
		return $collection;
	}
}
