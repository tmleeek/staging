<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/brand.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;

if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);

  			// Load by SKU

			       $str = $data[0];
			       $str1 = $data[1];
                   $str = str_replace("'",'"',$str);
                   $str1 = str_replace("'",'"',$str1);


                   $id = "";  $data1=''; $data2='';
                   $data1=mysql_escape_string($str);
                   $data2=mysql_escape_string($str1);
                   $sql = "INSERT INTO `temp_data_marque_collection` (marque, collection)
  		                                          VALUES ('".$data1."', '".$data2."')";

                   $write->query($sql);
                 $row++;
              }
	  }

           

echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>