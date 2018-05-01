<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/1_status.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');

$s_insert_row = 0;  $s_update_row =0;  $v_insert_row = 0;  $v_update_row =0;
$storeId=0;


if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {

                  $order_increment_id='';
                  $status='';
                  $order_increment_id=$data[0];
                  $status=$data[1];



                if(!empty($order_increment_id))
                {
                    $sql_data1 = 'UPDATE sales_flat_order_grid SET status = "'.$status.'" WHERE increment_id ='.$order_increment_id;

                    if($sql_data1)
                    {
                      $write->query($sql_data1);
                    }

                    $sql_data2='UPDATE sales_flat_order SET status = "'.$status.'" WHERE increment_id ='.$order_increment_id;
                    if($sql_data2)
                    {
                      $write->query($sql_data2);
                    }

                    $s_update_row++;
                }
                else
                {
                  echo $order_increment_id; echo '<br>'; $s_insert_row++;
                }

	       }
      }



echo "Total Updated orders:--".$s_update_row.'<br>';
echo "Total unavailable Orders:--".$s_insert_row.'<br>';

echo 'Done';

?>