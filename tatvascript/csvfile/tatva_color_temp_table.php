<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/color_new_add_temp.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;

if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);

  			// Load by SKU
			       $str = $data[1];
                   $str = str_replace(";",",",$str);
                   $id = "";
				   $sql = "INSERT INTO `tatva_color_temp` SET old_color = '".$data[0]."', color = '".$str."'";
                   $write->query($sql);
                 $row++;
              }
	  }



echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>