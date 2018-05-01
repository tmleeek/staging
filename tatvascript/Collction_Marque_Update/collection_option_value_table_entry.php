<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$collection_ids_array=array();

             $empty='TRUNCATE TABLE `collectionpages`';
             $write->query($empty);

             $collection_ids='SELECT manufacturer_id FROM `aitmanufacturers`';
             $collection_ids_array=$read->fetchAll($collection_ids);
            foreach($collection_ids_array as $ids)
            {
             $all_ids[] =$ids['manufacturer_id'];
            }
             $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'gamme_collection_new');


            foreach ($attribute->getSource()->getAllOptions(true, true) as $option){
              if(!$option['value']=='')
               {
                $marque_value[$option['label']]= $option['value'];
               }
             }
            /* single marque get collection from table */
            foreach($marque_value as $key=>$marque)
            {
               $status=2;
               if (in_array($marque, $all_ids, true))
               {
                 $status=1;
               }
               $sql = "INSERT INTO `collectionpages` (option_id, option_value,status)
  		                                          VALUES ('".$marque."', '".$key."','".$status."')";
               $write->query($sql);
               $row++;
            }
echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>