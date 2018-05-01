<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
ini_set ( 'memory_limit', '2048M' );


$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;

/* empty existing table */
$empty='TRUNCATE TABLE `marqueproducts`';
$write->query($empty);

$product_colls=Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('manufacturer')
    ->addAttributeToSelect('gamme_collection_new');
$product_colls->getSelect()->columns('GROUP_CONCAT(DISTINCT e.entity_id ORDER BY e.entity_id) AS related_products');
$product_colls->getSelect()->group('manufacturer');
$product_colls->getSelect()->group('gamme_collection_new');

//$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');
foreach($product_colls as $products)
{


    $marque = $products->getManufacturer();
    $collection_id = $products->getGammeCollectionNew();
    if(!empty($marque) && !empty($collection_id))
    {
        $storeId = 0;
        $product_ids =  $products->getRelatedProducts();
        if(!empty($product_ids))
        {
            //echo $sql="INSERT INTO `marqueproducts` SET `marque` = '".$marque."' , collection='".$collection_id."' , product_ids='".$product_ids."'";
            $sql="INSERT INTO `marqueproducts` SET `marque` = '".$marque."' , collection='".$collection_id."' , product_ids='".$product_ids."'";
            //echo "<br />";
            $write->query($sql);
            $row++;
        }
    }
}

echo "Total Inserted Item:--".$row.'<br>';
echo 'Done';
?>