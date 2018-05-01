<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/export_products_simple.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;  $storeId=0;
$cvarchartable='catalog_product_entity_varchar';

if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);
            // 211 is color attribute_id
  			// Load by SKU
                   $sku=''; $ids='';
			       $sku = $data[0];
                   $product_id = '';
                   if($sku!='')
                   {
                    echo $product_id = Mage::getModel("catalog/product")->getIdBySku($sku);  echo '<br>';
                   }

                  if($product_id!='' && $data[1]!='')
                  {
                     $data_sql="SELECT DISTINCT color FROM `tatva_color_temp` WHERE `old_color` ='".$data[1]."'";
                     echo $ids=$read->fetchOne($data_sql); echo '<br>';

                     if($ids!='' && $data[1]!='')
                     {
                       echo $sql = "INSERT INTO ".$cvarchartable." (entity_type_id, attribute_id, store_id, entity_id, value)
    		                                          VALUES ('4', '211', '".$storeId."', '".$product_id."', '".$ids."')";   echo '<br>--------------<br>';
                       $write->query($sql);
                       $row++;
                     }
                  }
                }
	         }

           

echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>