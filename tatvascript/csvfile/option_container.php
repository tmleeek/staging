<?php   
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/bundle_sku.csv';
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

                  if($product_id!='')
                  {

                
                      $sql="UPDATE ".$cvarchartable." SET `value` = 'container2' WHERE store_id=0 and attribute_id=109 and entity_id=".$product_id;

                       $write->query($sql);
                       $row++;

                  }
                }
	         }



echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>