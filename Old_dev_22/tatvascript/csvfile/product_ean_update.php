<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/export_products_simple_ean.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$s_insert_row = 0;  $s_update_row =0;  $v_insert_row = 0;  $v_update_row =0;
$storeId=0;
$cvarchartable='catalog_product_entity_varchar';

if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);

  			// Load by SKU
                   $sku='';   $p_id=''; $data1=''; $ean='';
			       $sku = $data[0];
			       $ean = $data[1];

                   if($sku!='')
                   {
                     $p_id = Mage::getModel('catalog/product')->getIdBySku(trim($sku));
                   }
                   if($p_id=='')
                   {
                     echo $sku; echo '<br>';
                   }
                  if(!empty($p_id))
                  {
                  $query1="select value_id from catalog_product_entity_varchar where entity_type_id= '4' AND attribute_id= '162' AND store_id = ".$storeId." AND entity_id = ".$p_id;             if($query1)
                   {
                      $data1=$read->FetchOne($query1);
                   }


                 }


                /* for ean start */
                if(!empty($data1))
                {
                    $sql_data1 = 'update catalog_product_entity_varchar  set value = "'.$ean.'" where  entity_id ='.$p_id.' and attribute_id=162 and store_id='.$storeId;
                    if($sql_data1)
                    {
                      $write->query($sql_data1);
                    }
                  $s_update_row++;
                }
                else
                {
                 if($p_id && $status)
                 {
                 $insert_status = "INSERT INTO ".$cvarchartable." (entity_type_id, attribute_id, store_id, entity_id, value)
  		                                          VALUES ('4', '162', '".$storeId."', '".$p_id."', '".$ean."')";
                 if($insert_status)
                 {
                   $write->query($insert_status);
                 }
                 $s_insert_row++;
                 }
                }
               /* status end */


               /* visibility start */


	     }
      }

echo "Total Updated Ean:--".$s_update_row.'<br>';
echo "Total Insert Ean:--".$s_insert_row.'<br>';

echo 'Done';

?>