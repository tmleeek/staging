<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/collection.csv';
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
				   $sql = "INSERT INTO `temp_data_marque_collection` SET `marque` = ".$data[0].",collection = ".$data[1]."";
                   $write->query($sql);
                 $row++;
              }
	  }

           

echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>