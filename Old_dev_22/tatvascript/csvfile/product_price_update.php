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
$file = Mage::getBaseDir() . '/tatvascript/product_price.csv';
$storeId=0;
$query= "";


$result = $read->fetchAll("SELECT attribute_id FROM eav_attribute eav
                       WHERE eav.entity_type_id = 4
                         AND eav.attribute_code = 'price'");

$attribute_id = 0;
if(is_array($result) && isset($result[0]["attribute_id"]) && $result[0]["attribute_id"]!="")
        $attribute_id = $result[0]["attribute_id"];


if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {


            $sku=''; $price=0; $product_id='';
            $sku=$data[0];
            $price=$data[1];
            if($sku!='')
            {
              $product_id = Mage::getModel("catalog/product")->getIdBySku($sku);
            }

            if(($product_id!='') && ($price!=0) && $attribute_id!==0)
            {

              $query .= "
                    UPDATE catalog_product_entity_decimal val
                    SET  val.value = $price
                    WHERE  val.attribute_id = $attribute_id
                      AND val.store_id = $storeId And val.entity_id = $product_id ;
                  ";
              $row++;
            }

      }
     $write->query($query);

}



echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>