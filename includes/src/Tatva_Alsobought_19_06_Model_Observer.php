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
class Tatva_Alsobought_Model_Observer
{
    /**
     * Prépare la sauvegarde des données de type link pour le type also bought
     *
     * @param   Varien_Event_Observer $observer
     * @return  Sqli_Alsobought_Model_Observer
     */
    public function initSaveProduct(Varien_Event_Observer $observer)
    {
    	$product = $observer->getEvent()->getProduct();
    	$request = $observer->getEvent()->getRequest();
    	$links = $request->getPost();
        echo "<pre>"; print_r($links); exit;
        if (isset($links['also_bought']) && !$product->getAlsoboughtReadonly()) {
            $product->setAlsoBoughtLinkData(Mage::helper('alsobought')->decodeInput($links['also_bought']));
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
    		// Sauvegarde des liens entre produits
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


    public function getSaveLinkProduct($final_product_id,$link_products)
    {
       $write = Mage::getSingleton("core/resource")->getConnection("core_write");
       $read= Mage::getSingleton('core/resource')->getConnection('core_read');
       $cvarchartable="catalog_product_link";

       foreach($link_products as $final_linked_id)
       {
            if($final_product_id!='' && $final_linked_id!='')
            {
            $link_id='';

            $sql_check_link_id='SELECT link_id FROM `catalog_product_link` WHERE `product_id` ='.$final_product_id.' AND `linked_product_id` ='.$final_linked_id.' Limit 1';
            $link_id=$read->FetchOne($sql_check_link_id);
            /* new data insert */
            if($link_id=='')
            {
              $sql = "INSERT INTO ".$cvarchartable." (product_id,linked_product_id,link_type_id)
    		                                          VALUES ('".$final_product_id."','".$final_linked_id."','100')";
              $write->query($sql);
            }
            else
            {
              $sql_update="UPDATE ".$cvarchartable." SET `linked_product_id` = '".$final_linked_id."' WHERE product_id='".$final_product_id."' and link_type_id=100";
              $write->query($sql_update);
            }

          }
       }
    }
}
