<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();

//header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/collection_alsoboutgh.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;  $qty_row=0;
$cvarchartable="catalog_product_link";
$cvarchartable1="catalog_product_link_attribute_int";
//echo "mauli"; exit;
if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {


                         $link_id='';
                         $product_id='';
                         $linked_product_id='';
                         $value=0;
                         $final_product_id='';
                         $final_linked_id='';
                         $final_value=0;
                         $final_value_temp='';


                         //$link_id=$data[0];
                         $product_id=$data[1];
                         $linked_product_id=$data[2];
                         $value=$data[3];

                        /* product id from lengow id */
                        if($data[1]!='')
                        {

                          $sql_product_id='SELECT entity_id FROM `catalog_product_entity_varchar` WHERE `attribute_id` =215 and value='.$data[1];
                           $final_product_id= $read->FetchOne($sql_product_id);
                        }



                       /* linked product id from lengow id */
                        if($data[2]!='')
                        {

                          $sql_link_product_id='SELECT entity_id FROM `catalog_product_entity_varchar` WHERE `attribute_id` =215 and value='.$data[2];
                          $final_linked_id= $read->FetchOne($sql_link_product_id);
                        }

                         /* check data is already inserted or not */
                          if($final_product_id!='' && $final_linked_id!='')
                          {

                          $sql_check_link_id='SELECT link_id FROM `catalog_product_link` WHERE `product_id` ='.$final_product_id.' AND `linked_product_id` ='.$final_linked_id.' Limit 1';
                          $link_id=$read->FetchOne($sql_check_link_id);



                         /* insert in catalog_product_link */
                         if($final_product_id!='' && $final_linked_id!='' && $link_id=='')
                         {
				            $sql = "INSERT INTO ".$cvarchartable." (product_id,linked_product_id,link_type_id)
    		                                          VALUES ('".$final_product_id."','".$final_linked_id."','100')";
                            $write->query($sql);

                             $sql_check_link_id='SELECT link_id FROM `catalog_product_link` WHERE `product_id` ='.$final_product_id.' AND `linked_product_id` ='.$final_linked_id.' Limit 1';
                           $link_id=$read->FetchOne($sql_check_link_id);
                            $row++;
                         }
                          echo $link_id; echo '<br>';
                         /* check qty already inserted or not */
                           if($link_id!='')
                           {
                           $qty_sql='SELECT value FROM `catalog_product_link_attribute_int` WHERE `product_link_attribute_id` =6 AND `link_id` ='.$link_id;
                           $final_value_temp=$read->FetchOne($qty_sql);
                           }


                         /* if qty not insterted then insert data */
                         if($final_value_temp=='')
                         {
                           $sql_qty = "INSERT INTO ".$cvarchartable1." (product_link_attribute_id,link_id,value)
    		                                          VALUES ('6','".$link_id."','".$value."')";
                           $write->query($sql_qty);
                           $qty_row++;
                         }

                        } 

              }
	  }



echo "Total Inserted Link Item:--".$row.'<br>';
echo "Total Inserted Link2 Item:--".$qty_row.'<br>';
echo 'Done';

?>