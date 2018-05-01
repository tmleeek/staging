<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
//header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/importlengowid_1.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;  $storeId=0;     $update=0;  $i=0;
$cvarchartable='catalog_product_entity_varchar';

if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);

  			// Load by SKU
                   $sku=''; $product_id=''; $lengow_id='';
			       $sku = $data[0];

                   $id = "";
                   $id = $data[1];

                   if($sku!='')
                   {
                     echo $product_id = Mage::getModel("catalog/product")->getIdBySku($sku);   echo '<br>';
                   }



                   if($product_id!='' && $id!='')
                   {
                       $check_lengow_id='SELECT value FROM `catalog_product_entity_varchar` WHERE `attribute_id` = 215 and entity_id='.$product_id;
                        echo $i.'--'.$lengow_id=$read->fetchOne($check_lengow_id); echo '<br>';
                      $i++;
                     if($lengow_id!='')
                     {
                       	  $lengow_sql="UPDATE ".$cvarchartable." SET `value` = '".$id."' WHERE attribute_id=215 and entity_id=".$product_id;
                          $write->query($lengow_sql);
                          $update++;
                     }
                     else
                     {
                          echo $sql = "INSERT INTO ".$cvarchartable." (entity_type_id, attribute_id, store_id, entity_id, value)
      		                                          VALUES ('4', '215', '".$storeId."', '".$product_id."', '".$id."')";  echo '<br>';
                          //$write->query($sql);
                          $row++;
                     }
                   }

              }
	  }



echo "Total Updated lengow Id:--".$update.'<br>';
echo "Total inserted lengow Id:--".$row.'<br>';
echo 'Done';

?>