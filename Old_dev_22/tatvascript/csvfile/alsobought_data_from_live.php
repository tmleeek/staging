<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
//header('Content-type: text/html; charset=utf-8');

//echo "hi"; exit;
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$data='';
$val=array();

$sql='SELECT * FROM `catalog_product_link` WHERE `link_type_id` =100 LIMIT 10';
$data=$read->fetchAll($sql);
$filename = "collection_alsoboutgh.csv";
//echo "<pre>"; print_r($data); exit;
  $fp = fopen($filename, "w");
   $line = ""; $qty_value=0;
   foreach($data as $datas)
   {
     $link_temp_id='';
     $link_temp_id=$datas['link_id'];
     if($link_temp_id!='')
     {
      echo $link_sql='SELECT value FROM `catalog_product_link_attribute_int` WHERE `product_link_attribute_id` =9 and link_id='.$link_temp_id;
      echo $qty_value=$read->FetchOne($link_sql);  echo '<br>';
     }


     $line.= '"' . str_replace('"', '""', $datas['link_id']) . '"'.','.'"' . str_replace('"', '""', $datas['product_id']) . '"'.','.'"' . str_replace('"', '""', $datas['linked_product_id']) . '"'.','.'"' . str_replace('"', '""', $qty_value) . '"';

	 $line.= "\n";

     $row++;
   }
//echo "<pre>"; print_r($line);
fputs($fp, $line);
fclose($fp);
echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>