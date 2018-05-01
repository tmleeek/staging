<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
ini_set ( 'memory_limit', '2048M' );


$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read = Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$cvarchartable = 'aitmanufacturers';
$store_id = Mage :: app()->getStore();

$row = 0;

$product_colls=Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('manufacturer');
//$product_colls->addAttributeToFilter('gamme_collection_new', array('notnull' => true));
$product_colls->getSelect()->where('e.gamme_collection_new IS NOT NULL');
$product_colls->getSelect()->columns('GROUP_CONCAT(DISTINCT e.gamme_collection_new ORDER BY e.gamme_collection_new) AS related_collection');
$product_colls->getSelect()->group('manufacturer');

//echo  $product_colls->getSelect();
//exit;
foreach($product_colls as $products)
{
    $manufacture = $products->getManufacturer();
    if(!empty($manufacture))
    {
        $storeId = 0;

        $select = "select * from aitmanufacturers where manufacturer_id = '".$manufacture."' LIMIT 1";
        $data = $read->fetchRow($select);

        if($data!='')
        {
            //echo $update_data = "Update ".$cvarchartable." set collection = '".$products->getRelatedCollection()."' where manufacturer_id = ".$manufacture;
            $update_data = "Update ".$cvarchartable." set collection = '".$products->getRelatedCollection()."' where manufacturer_id = ".$manufacture;
            $write->query($update_data);
            //echo "<br /><br />";

        }
        else
        {
            $urlKey = Mage::helper('aitmanufacturers')->toUrlKey($products->getManufacturerValue());

            $select1 = "select url_key from aitmanufacturers";
            $data1 = $read->fetchCol($select1);

            while (in_array($urlKey, $data1))
            {

                $urlKey .= rand(0, 99);
            }

            //echo $aitmanufacturers_data = 'INSERT INTO '.$cvarchartable.' (manufacturer_id,title,show_brief_image,show_list_image,url_key,root_template,featured,status,sort_order,collection,stores)
            //VALUES ("'.$manufacture.'","'.$products->getManufacturerValue().'",1,1,"'.$urlKey.'","two_columns_left",0,1,0,"'.$products->getRelatedCollection().'","'.array($storeId).'")';

            $aitmanufacturers_data = 'INSERT INTO '.$cvarchartable.' (manufacturer_id,title,show_brief_image,show_list_image,url_key,root_template,featured,status,sort_order,collection,stores)
            VALUES ("'.$manufacture.'","'.$products->getManufacturerValue().'",1,1,"'.$urlKey.'","two_columns_left",0,1,0,"'.$products->getRelatedCollection().'","'.array($storeId).'")';
            $write->query($aitmanufacturers_data);
            //echo "<br /><br />";
            $lastid =  $write ->lastInsertId();
            //echo "<br /><br />";
            $aitmanufacturers_store = 'INSERT INTO aitmanufacturers_stores (id,manufacturer_id,store_id) VALUES ('.$lastid.','.$manufacture.',0)';

            $write->query($aitmanufacturers_store);
            //echo "<br /><br />";
            //exit;
            //echo "<br /><br />";
        }
        $row++;
    }
}
echo "Total Inserted Marque Item:--".$row.'<br>';

$product_colls1=Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('gamme_collection_new');
$product_colls1->getSelect()->where('e.gamme_collection_new IS NOT NULL');
$product_colls1->getSelect()->group('gamme_collection_new');


//echo  $product_colls1->getSelect();
//exit;
foreach($product_colls1 as $product)
{
    $collection = $product->getGammeCollectionNew();
    if(!empty($collection))
    {
        $storeId = 0;


        $select = "select * from aitmanufacturers where manufacturer_id = '".$collection."' LIMIT 1";
        $data = $read->fetchRow($select);

        $urlKey = Mage::helper('aitmanufacturers')->toUrlKey($product->getGammeCollectionNewValue());

        while (in_array($urlKey, $data1))
        {
            $urlKey .= rand(0, 99);
        }

        if($data!='')
        {

            $update_data = "Update ".$cvarchartable." set url_key = '".$urlKey."' where manufacturer_id = ".$collection;
            $write->query($update_data);
            //echo "<br /><br />";

        }
        else
        {
            $select1 = "select url_key from aitmanufacturers";
            $data1 = $read->fetchCol($select1);

            //echo $aitmanufacturers_data = 'INSERT INTO '.$cvarchartable.' (manufacturer_id,title,show_brief_image,show_list_image,url_key,root_template,featured,status,sort_order,collection,stores)
            //VALUES ("'.$collection.'","'.$product->getGammeCollectionNewValue().'",1,1,"'.$urlKey.'","two_columns_left",0,1,0,"","'.array($storeId).'")';

            $aitmanufacturers_data = 'INSERT INTO '.$cvarchartable.' (manufacturer_id,title,show_brief_image,show_list_image,url_key,root_template,featured,status,sort_order,collection,stores)
            VALUES ("'.$collection.'","'.$product->getGammeCollectionNewValue().'",1,1,"'.$urlKey.'","two_columns_left",0,1,0,"","'.array($storeId).'")';
            $write->query($aitmanufacturers_data);
            //echo "<br /><br />";
            $lastid =  $write ->lastInsertId();
            //echo "<br /><br />";
            $aitmanufacturers_store = 'INSERT INTO aitmanufacturers_stores (id,manufacturer_id,store_id) VALUES ('.$lastid.','.$collection.',0)';

            $write->query($aitmanufacturers_store);
            //echo "<br /><br />";
            //exit;
            //echo "<br /><br />";
        }
        $row++;
    }
}
echo "Total Inserted Collection Item:--".$row.'<br>';

?>