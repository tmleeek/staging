<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
//header('Content-type: text/html; charset=utf-8');

//echo "hi"; exit;
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$data=''; $update=0;
$val=array();
$file = Mage::getBaseDir() . '/tatvascript/ean.csv';
$storeId=0;
$query= "";
$cvarchartable='catalog_product_entity_varchar';



$result = $read->fetchAll("SELECT attribute_id FROM eav_attribute eav
                         WHERE eav.entity_type_id = 4
                         AND eav.attribute_code = 'ean13'");

$attribute_id = 0;
if(is_array($result) && isset($result[0]["attribute_id"]) && $result[0]["attribute_id"]!="")
        $attribute_id = $result[0]["attribute_id"];


if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {


            $sku=''; $amazon=''; $product_id='';$amazon_icon='';

            $sku=$data[0];
            $amazon=$data[1];
            if($sku!='')
            {
              $product_id = Mage::getModel("catalog/product")->getIdBySku($sku);
            }

            if(($product_id!='') && ($amazon!=0) && $attribute_id!==0)
            {

              $check_amazon_icon='SELECT value FROM `catalog_product_entity_varchar` WHERE `attribute_id` ='.$attribute_id.' and entity_id='.$product_id;
              $amazon_icon=$read->fetchOne($check_amazon_icon);

                     if($amazon_icon!='')
                     {
                       	  $lengow_sql="UPDATE ".$cvarchartable." SET `value` = '".$amazon."' WHERE attribute_id=".$attribute_id." and entity_id=".$product_id;
                          $write->query($lengow_sql);
                          $update++;
                     }
                     else
                     {
                           $sql = "INSERT INTO ".$cvarchartable." (entity_type_id, attribute_id, store_id, entity_id, value)
      		                                          VALUES ('4', '".$attribute_id."', '".$storeId."', '".$product_id."', '".$amazon."')";
                          $write->query($sql);
                          $row++;
                     }

            }

      }


}



echo "Total Inserted Item:--".$row.'<br>';
echo "Total Updated Item:--".$update.'<br>';
echo 'Done';

?>