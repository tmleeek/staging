<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();



$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');

$row = 0; $increment_ids=array();
$check_str='12'; $new_str='2';
$cvarchartable='catalog_product_entity_varchar';
$order = Mage::getModel('sales/order');


    $order_ids="SELECT increment_id,entity_id FROM `sales_flat_order` where (created_at BETWEEN '2014-11-05 00:00:00' AND '2014-11-13 00:00:00')";
    $increment_ids[]=$read->FetchAll($order_ids);
	

    foreach($increment_ids[0] as $increment_id)
    {
       $id=''; $entity_id='';
       $id=$increment_id['increment_id'];
       $entity_id=$increment_id['entity_id'];

       if($id!='')
       {
         $str=substr($id, 0, 2);
         if($str==$check_str)
         {
            $temp='';
            $temp=substr($id, 2);
            $new_id=$new_str.$temp;
            $sales_grid="UPDATE `sales_flat_order_grid` SET `increment_id` = '".$new_id."' WHERE entity_id =".$entity_id;
            $write->query($sales_grid);

            $sales_flat= "UPDATE `sales_flat_order` SET `increment_id` = '".$new_id."' WHERE entity_id =".$entity_id;
            $write->query($sales_flat);

            echo  $id; echo '--'; $new_str.$temp; echo '<br>';
            $row++;
         }
       }
    }
echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>