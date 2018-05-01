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

$filename = "new_weee_data_with_sku.csv";
$fp = fopen($filename, "w");
$line = "";

//echo "mauli"; exit;
if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {

                         $id='';  $sku='';
                        if($data[0]!='')
                        {

                          $sql_product_id='SELECT entity_id FROM `catalog_product_entity_varchar` WHERE `attribute_id` =215 and value='.$data[0];
                          $id= $read->FetchOne($sql_product_id);
                          $sku = Mage::getModel('catalog/product')->load($id)->getSku();

                          if($sku!='')
                          {
                            $line.= '"' . $sku . '"'.','.'"' . $data[1] . '"'.','.'"' . $data[2] . '"'.','.'"' . $data[3] . '"'.','.'"' . $data[4] . '"';
                            $line.= "\n";
                            $row++;
                          }
                        }





              }
	  }


fputs($fp, $line);
fclose($fp);
echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>