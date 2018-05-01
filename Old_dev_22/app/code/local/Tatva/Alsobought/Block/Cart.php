<?php
/**
 * created : 21 aout 2009
 * Cart also bought list
 * 
 * 
 * @category SQLI
 * @package Sqli_Alsobought
 * @author sgautier
 * @copyright SQLI - 2009 - http://www.tatva.com
 */

/**
 * 
 * @package Sqli_Alsobought
 */

/**
 * 
 *
 * @category   Sqli
 * @package    Sqli_Alsobought
 * @author     sgautier
 */
class Tatva_Alsobought_Block_Cart extends Mage_Checkout_Block_Cart_Crosssell
{
	public function __construct() {
		$this->addPriceBlockType('simple', 'tatvacatalog/product_price_profile1', 'tatvacatalog/product/price_complete.phtml');
		$this->addPriceBlockType('configurable', 'tatvacatalog/product_price_profile1', 'tatvacatalog/product/price_complete.phtml');
		$this->addPriceBlockType('virtual', 'tatvacatalog/product_price_profile1', 'tatvacatalog/product/price_complete.phtml');
		$this->addPriceBlockType('bundle', 'tatvacatalog/product_price_profile1', 'tatvacatalog/product/price_complete.phtml');
		return parent::__construct();
	}
	
    /**
     * Items quantity will be capped to this value
     *
     * @var int
     */
    protected $_maxItemCount = 3;

    /**
     * Get crosssell products collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
     */
    protected function _getCollection()
    {
        $collection = Mage::getModel('catalog/product_link')->useAlsoBoughtLinks()
            ->getProductCollection()
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addAttributeToSelect('marque')
            ->addStoreFilter()
            ->setPageSize($this->_maxItemCount);
        $this->_addProductAttributesAndPrices($collection);

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        return $collection;
    }
    
	/**
     * Get brand
     */
	public function getBrand($brandId) {
		$storeId = Mage::app()->getStore()->getId();
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,$this->helper('brand')->getBrandAttributeCode());
		
		$brands = Mage::getModel('eav/entity_attribute_option')	->getCollection()
																->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
																->setIdFilter($brandId)
																->setStoreFilter($storeId, false)
																->load();
														
		foreach ($brands as $brand)
			return $brand;
	}
}
