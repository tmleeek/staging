<?php
/*
 * SELECT `main_table`.`order_id` , `catalog/product`.entity_id
FROM `sales_flat_order_item` AS `main_table`
INNER JOIN `erp_sales_flat_order_item` AS `AdvancedStock/SalesFlatOrderItem` ON item_id = esfoi_item_id
INNER JOIN `catalog_product_entity` AS `catalog/product` ON entity_id = product_id
WHERE `main_table`.`order_id` =100034283
LIMIT 0 , 30
collection = Florence = getGammeCollectionNew() = 5955
brand = Amefa = getManufacturer() = 33
 */
chdir(dirname(__FILE__));
require 'app/Mage.php';
$storeId = Mage::app()->getStore()->getId();
$categoryIds = array();
$flag = 0;

/*$categories = Mage::getModel('catalog/category')->getCollection()
    ->addAttributeToSelect('entity_id')//or you can just add some attributes
    ->addAttributeToFilter('level', 2)//2 is actually the first level
    ->addAttributeToFilter('is_active', 1);

foreach($categories as $cat)
{
    $categoryIds[] = $cat['entity_id'];
}*/
$collection = Mage::getModel('catalog/product')->getCollection();
$collection->getSelect()
     ->reset(Zend_Db_Select::COLUMNS)
     ->columns('entity_id');
//$collection->getSelect()->limit(9000,6565);                                
//echo count($collection);die();
//echo "<pre>";print_r($collection->getData());die();
$i=1;
foreach($collection as $data)
{
    $productId = $data['entity_id'];
    $product = Mage::getModel('catalog/product')->load($productId);
    $productcollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter("gamme_collection_new",array("eq"=>$product->getGammeCollectionNew()))->addAttributeToFilter("manufacturer",array("eq"=>$product->getManufacturer()))->addAttributeToFilter('entity_id', array('neq' => $productId));
    $upsellLinks = array();
    foreach($productcollection as $data)
    {
        $upsellLinks[$data['entity_id']] = array('position'=>'');
    }
    $product->setUpSellLinkData($upsellLinks);
    if($product->save())
    {
        echo "crosssell for ".$productId." has been updated"."total count".$i. PHP_EOL;
        $flag = 1;
    }
    $i++;
}
if($flag == 1)
{
    echo "All products updated successfully";
}
?>