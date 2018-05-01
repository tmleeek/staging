<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();




$file = Mage::getBaseDir() . '/tatvascript/collection_alsoboutgh.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;  $qty_row=0;

$temp1= "optionimages_product_option_type_image";



                          $sql_product_id='SELECT DISTINCT(option_type_id) , option_type_image_id,store_id, image FROM `optionimages_product_option_type_image_old` GROUP BY option_type_id';
                          $final_product_id= $read->FetchAll($sql_product_id);


                         foreach($final_product_id as $data)
                         {
                          $sql_qty = "INSERT INTO ".$temp1." (option_type_id,store_id,image)
    		                                          VALUES ('".$data['option_type_id']."','".$data['store_id']."','".$data['image']."')";
                           $write->query($sql_qty);
                           $row++;  
                         }





echo "Total Deleted Item:--".$row.'<br>';

echo 'Done';

?>