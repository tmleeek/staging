<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
//header('Content-type: text/html; charset=utf-8');

//$file = Mage::getBaseDir() . '/tatvascript/collections_to_erase.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
/*
if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);

  			// Load by SKU
			       $str = $data[0];
                   $str = str_replace("'",'"',$str); echo $str.'<br>';
                   $id = "";
				   $sql = "DELETE FROM `eav_attribute_option_value` WHERE `eav_attribute_option_value`.`value` = '".$str."' LIMIT 1";
                   if($sql)
                   {
                   $write->query($sql);
                   $row++;
                   }
              }
	  }
*/

$sql='SELECT  option_id FROM `eav_attribute_option` where attribute_id=206';
$ids[]=$read->FetchAll($sql);
foreach($ids[0] as $temp_id) {
foreach($temp_id as $id)
{
   $temp='';
   $sql_option='SELECT * FROM `eav_attribute_option_value` WHERE `option_id` ='.$id;
   $temp=$read->FetchOne($sql_option);
   if($temp=='')
   {
     $delete='DELETE FROM `eav_attribute_option` WHERE `eav_attribute_option`.`option_id` ='.$id.' LIMIT 1';
     $write->query($delete); $row++;
     echo $id; echo '<br>';
   }
}
}

echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>