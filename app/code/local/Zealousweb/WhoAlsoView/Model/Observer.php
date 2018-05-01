<?php

session_start();

class Zealousweb_WhoAlsoView_Model_Observer {

    public function WhoAlsoView(Varien_Event_Observer $observer) {

        $ids = array();
        $names = array();
        $sku = array();
        $qty = array();
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $orderedItems = $order->getAllVisibleItems();
        foreach ($orderedItems as $item) 
        {
            $productmodel = Mage::getModel("catalog/product")->load($item->getData('product_id'));
            $ids[] = $productmodel->getId();
            $names[] = $productmodel->getName();
            $sku[] = $productmodel->getSku();
            $qty[] = $item->getQtyOrdered(); 
        }
        $comma_separated_id = implode(",", $ids);
        $comma_separated_sku = implode(",", $sku);
        $comma_separated_qty = implode(",", $qty);
        
        //$model = Mage::getModel('whoalsoview/whoalsoview');
        //$model->setData(array('product_id' => addslashes($id), 'product_sku' => addslashes($sku)));
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "INSERT INTO `who_also_view` (`id` ,`product_id` ,`product_sku`,`product_quantity`)VALUES (NULL , '".$comma_separated_id."', '".$comma_separated_sku."', '".$comma_separated_qty."')";
        $write->query($query);
        
        //$model->save();
    }
    public function automaticupsell(Varien_Event_Observer $observer) 
    {
        $product = $observer->getProduct();
        $productId = $product->getId();
        $productcollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter("gamme_collection_new",array("eq"=>$product->getGammeCollectionNew()))->addAttributeToFilter("manufacturer",array("eq"=>$product->getManufacturer()))->addAttributeToFilter('entity_id', array('neq' => $productId));
        $upsellLinks = array();
        foreach($productcollection as $data)
        {
            $upsellLinks[$data['entity_id']] = array('position'=>'');
        }
        $product->setUpSellLinkData($upsellLinks);
    }

}
