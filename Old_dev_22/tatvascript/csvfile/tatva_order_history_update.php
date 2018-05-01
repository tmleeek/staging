<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
$file = Mage::getBaseDir() . '/tatvascript/tatva_order_qty_collection_31_12_2010.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$rows='';

      if (($handle = fopen($file, "r")) !== FALSE)
      {

        while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE)
        {

         $increment_id='';
         $sku='';
         $qty_order='';
         $qty_invoice='';
         $qty_shipped='';
         $qty_cancel='';
         $qty_refund='';

         $increment_id=$data[0];
         $sku=$data[1];
         $qty_order=$data[2];
         $qty_invoice=$data[3];
         $qty_shipped=$data[4];
         $qty_cancel=$data[5];
         $qty_refund=$data[6];



           if(($sku!='') && ($increment_id!='') && ($qty_order!=''))
           {

           $query="UPDATE `sales_flat_order_item` s JOIN `sales_flat_order` o ON s.order_id=o.entity_id SET s.qty_shipped='".$qty_shipped."',s.qty_invoiced='".$qty_invoice."',s.qty_canceled='".$qty_cancel."',s.qty_refunded='".$qty_refund."' where s.sku='".$sku."' and o.increment_id='".$increment_id."'";

echo $query.'<br>';
             $write->query($query);
             $row++;
           }

        }
       }
echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>