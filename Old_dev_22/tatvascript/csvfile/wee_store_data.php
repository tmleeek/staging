<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
//header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/collection_weee_live.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$cvarchartable="weee_tax";
//echo "mauli"; exit;
if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {

                         $id='';
                        if($data[0]!='')
                        {

                          $sql_product_id='SELECT entity_id FROM `catalog_product_entity_varchar` WHERE `attribute_id` =215 and value='.$data[0];
                          echo  $id= $read->FetchOne($sql_product_id);
                        }
				         $sql = "INSERT INTO ".$cvarchartable." (website_id,entity_id,country,value,state,attribute_id,entity_type_id)
    		                                          VALUES ('0', '".$id."','".$data[1]."', '".$data[2]."','".$data[3]."','157', '4')";
                                                      echo '<br>--';
                        $write->query($sql);
                        $row++;



              }
	  }



echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>