<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');
 
$file = Mage::getBaseDir() . '/tatvascript/product_status_change_31_10.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$s_insert_row = 0;  $s_update_row =0;  $v_insert_row = 0;  $v_update_row =0;
$storeId=0;
$cvarchartable='catalog_product_entity_int';

if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);

  			// Load by SKU
                   $sku='';   $p_id=''; $data1=''; $data2=''; $status=''; $visiblity='';
			       $sku = $data[0];
			       $status = $data[1];
			       //$visiblity = $data[2];
                   if($sku!='')
                   {
                     $p_id = Mage::getModel('catalog/product')->getIdBySku(trim($sku));
                   }
                   if($p_id=='')
                   {
                     echo $sku; echo '<br>';  $s_insert_row++;
                   }



                /* for status start */
                if(!empty($p_id))
                {
                    $sql_data1 = 'update catalog_product_entity_int  set value = "'.$status.'" where  entity_id ='.$p_id.' and attribute_id=96 and store_id='.$storeId;
                    if($sql_data1)
                    {
                      $write->query($sql_data1);
                    }
                  $s_update_row++;
                }

	       }
      }



echo "Total Updated Status:--".$s_update_row.'<br>';
echo "Total Insert Status:--".$s_insert_row.'<br>';

echo 'Done';

?>