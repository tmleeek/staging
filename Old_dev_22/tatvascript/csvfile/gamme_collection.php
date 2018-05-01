<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/collection_matrix.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;

if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);

  			// Load by SKU
			       $str = $data[0];
                   $str = str_replace("'",'"',$str);echo $str.'<br>';
                   $id = "";
				   $sql = "INSERT INTO `eav_attribute_option` SET `attribute_id` = 210, `sort_order` = 0";
				   
				   $write->query($sql);
				   $sql1 = "SELECT option_id FROM eav_attribute_option ORDER BY option_id DESC LIMIT 1";				   
				   $id = $read->FetchOne($sql1);
                   $sql2 = "INSERT INTO `eav_attribute_option_value` set `option_id` = ".$id.", `store_id` = 0, `value` = '".$str."'";
					//$sql2 = "INSERT INTO `eav_attribute_option_value` set `option_id` = ".$id.", `store_id` = 0, `value` = 'Râpe à fromage'";
					$write->query($sql2);
                   
                 $row++;
              }
	  }

           

echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>