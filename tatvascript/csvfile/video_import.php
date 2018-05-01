<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/collection_video_url.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$cvarchartable='tatva_video_item';

if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);

  			// Load by SKU
                   $sku='';
			       $sku = $data[0];
                   $sku = str_replace("'",'"',$sku);echo $sku.'<br>';
                   $id = "";
                   if($sku!='')
                   {
                     echo $product_id = Mage::getModel("catalog/product")->getIdBySku($sku);  echo '<br>';
                   }
				    $sql = "INSERT INTO ".$cvarchartable." (product_id,video_url)
  		                                          VALUES ('".$product_id."', '".$data[1]."')";
                   $write->query($sql);
                   $row++;
              }
	  }



echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>