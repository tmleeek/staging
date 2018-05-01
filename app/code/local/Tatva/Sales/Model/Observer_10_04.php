<?php
/**
 * created : 28 août 2009
 *
 * @category SQLI
 * @package Sqli_Sales
 * @author lbourrel
 * @copyright SQLI - 2009 - http://www.tatva.com
 */

/**
 *
 * @package Sqli_Sales
 */
class Tatva_Sales_Model_Observer
{

	/**
	 * Sauvegarde le montant de la taxe des frais de port
	 * @param $observer
	 */
    public function savePercentTvaShipping($observer) {

    	$quote = $observer->getEvent()->getQuote();

        foreach ($quote->getAllAddresses() as $address) {

        	$store = $address->getQuote()->getStore();
	        $custTaxClassId = $address->getQuote()->getCustomerTaxClassId();
	        $taxCalculationModel = Mage::getSingleton('tax/calculation');
	        $request = $taxCalculationModel->getRateRequest($address, $address->getQuote()->getBillingAddress(), $custTaxClassId, $store);
	        $shippingTaxClass = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $store);

	        if ($shippingTaxClass) {
	        	$rate = $taxCalculationModel->getRate($request->setProductClassId($shippingTaxClass));
	        	$address->setPercentTaxShipping($rate);
	        }

        }

    }

    /**
     * Lors de l'annulation d'une commande, les stocks des fournisseurs sont ré-incrémentés
     * @param $observer
     */
    public function cancelSupplierItem($observer){
    	$item = $observer->getEvent()->getItem();
    	$supplierId = $item->getSupplierId();
    	if($supplierId){
    		$stocksItem = Mage::getModel('tatvainventory/item')->getCollection()
    			->addSupplierIdFilter($supplierId)
    			->addProductIdFilter($item->getProductId());

    		foreach($stocksItem as $stock){
    			$stock->setCurrentStock($stock->getCurrentStock() + $item->getQtyOrdered());
    			$stock->save();
    		}
    	}
    }

    /**
     * Enregistrement la collection et la marque du produit à l'ajout d'un produit au panier (quote_item)
     * @param $observer
     */
    public function quoteItemSetProduct($observer){
    	$quoteItem = $observer->getEvent()->getItem();

    	if($quoteItem ){

    		$gamme = Mage::getResourceSingleton('tatvacatalog/product')->selectValue($quoteItem->getProductId(), 'gamme_collection', Mage::app()->getStore()->getId());
    		$marque = Mage::getResourceSingleton('tatvacatalog/product')->selectValue($quoteItem->getProductId(), 'marque', Mage::app()->getStore()->getId());
    		$marque = Mage::helper('tatvacatalog')->getBrand($marque);
    		if($marque){
    			$marque = $marque->getValue();
    		}else{
    			$marque =  "";
    		}

     		$quoteItem->setMarque($marque);
    		$quoteItem->setGammeCollection($gamme);
    		return $quoteItem;
    	}
    }

    /**
     * Enregistrement la collection et la marque du produit
     * @param $observer
     */
    public function orderItemSetProduct($observer){
    	$orderItem = $observer->getEvent()->getItem();
    	if($orderItem ){

    		$gamme = Mage::getResourceSingleton('tatvacatalog/product')->selectValue($orderItem->getProductId(), 'gamme_collection', Mage::app()->getStore()->getId());
    		$marque = Mage::getResourceSingleton('tatvacatalog/product')->selectValue($orderItem->getProductId(), 'marque', Mage::app()->getStore()->getId());
    		$marque = Mage::helper('tatvacatalog')->getBrand($marque);
    		if($marque){
    			$marque = $marque->getValue();
    		}else{
    			$marque =  "";
    		}

    		$orderItem->setMarque($marque);
    		$orderItem->setGammeCollection($gamme);
    		return $orderItem;
    	}
    }

}

