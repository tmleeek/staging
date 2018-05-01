<?php
/**
 * Order item render block
 *
 * @category    Sqli
 * @package     Sqli_Sales
 * @author      zimzourh
 */
class Tatva_Sales_Block_Order_Item_Renderer_Default extends Mage_Sales_Block_Order_Item_Renderer_Default
{
	/**
     * Get brand
     */
	public function getBrand($item) {
		$storeId = Mage::app()->getStore()->getId();
		$productEntityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
		$brandAttribute = Mage::getModel('eav/entity_attribute')->loadByCode($productEntityTypeId,$this->helper('brand')->getBrandAttributeCode());
		
		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addIdFilter($item->getProductId())
			->addAttributeToSelect('marque');
		if($collection && $collection->getSize()){
			$product = 	$collection->getFirstItem();
			
			$brands = Mage::getModel('eav/entity_attribute_option')	->getCollection()
						->addFieldToFilter('attribute_id',array('='=>$brandAttribute->getAttributeId()))
						->setIdFilter($product->getMarque())
						->setStoreFilter($storeId, false)
						->load();
			return $brands->getFirstItem();
		}
		return Mage::getModel('eav/entity_attribute_option');
	}
	
	/**
     * Return URL of order item.
     *
     * @return string
     */
    public function getProductUrl()
    {
        $collection = Mage::getModel('catalog/product')
			->getCollection()
			->addIdFilter($this->getItem()->getProductId());
			
    	if($collection && $collection->getSize()){
			$product = 	$collection->getFirstItem();
			
			return $product->getProductUrl();
		}
		
		return "#";
    }
}