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
class Tatva_Freegift_Model_Observer
{
   public function initSaveProduct(Varien_Event_Observer $observer)
    {
    	$product = $observer->getEvent()->getProduct();
    	$request = $observer->getEvent()->getRequest();
    	$links = $request->getPost('links');
        //echo "<pre>"; print_r($links); exit;
        if (isset($links['freegift'])) {
            $product->setAlsoBoughtLinkData(Mage::helper('freegift')->decodeInput($links['freegift']));
        }
    	return $this;
    }


   public function updateAlsobought(Varien_Event_Observer $observer)
    {
    	$order = $observer->getEvent()->getOrder();
    	$items = $order->getAllItems();
    	if(sizeof($items) <= 1) {
    		// Un seul produit commandé => aucun lien produit à alimenter
    		return $this;
    	}
    	foreach($items as $item) {
    		//$array = array ( 2 => array ( 'position' => 123 ), 1 => array ( [position] => null) );

    		$product = Mage::getModel('catalog/product')->setId($item->getProductId());

    		// Initialisation du tableau avec les valeurs courantes de liens de produits
    		$array = $this->_getArrayCurrentLinks($product);

    		foreach($items as $item2) {
    			if($item->getProductId() == $item2->getProductId()) {
    				// Pas d'association du produit avec lui-même
    				continue;
    			}
    			if(isset($array[$item2->getProductId()])) {
    				$array[$item2->getProductId()]['position']++;
    			} else {
    				$array[$item2->getProductId()] = array('position' => 1);
    			}
    		}

    		$product->setAlsoBoughtLinkData($array);

    		Mage::getModel('catalog/product_link')->saveProductRelations($product);
    	}
    	return $this;
    }

    private function _getArrayCurrentLinks($product=null) {
    	$res = array();
    	if($product == null) {
    		return $res;
    	}
		$productLinks = $product->getAlsoBoughtLinkCollection();

		foreach($productLinks as $link) {
			$res[$link->getLinkedProductId()] = array('position' => $link->getPosition());
		}


		return $res;
    }
}
