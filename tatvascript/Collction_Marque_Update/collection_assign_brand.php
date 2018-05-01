<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

//$file = Mage::getBaseDir() . '/tatvascript/collection.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');


/*$con = mysql_connect("127.0.0.1","azb_pre_usr","Nu64*DSA");
//$con = mysql_connect("127.0.0.1","root","F3632tv!");
if (!$con)
 {
 die('Could not connect: ' . mysql_error());
 }
//exit;
mysql_select_db("newdevazb", $con);*/



$row = 0;


            $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');
            foreach ($attribute->getSource()->getAllOptions(true, true) as $option){
              if(!$option['value']=='')
               {
                $marque_value[$option['label']]= $option['value'];
               }
             }
            //echo '<pre>';print_r($marque_value);exit;
            foreach($marque_value as $key=>$marque)
            {  echo $key;  echo '<br>----<br>';
               $collection_name_data_sql= "SELECT DISTINCT collection from `temp_data_marque_collection` where marque='".mysql_escape_string($key)."'";
               $collection_name_data=$read->fetchAll($collection_name_data_sql);
               $temp_coll_ids=array();  

              foreach($collection_name_data as $name)
              {
                //echo "<pre>"; print_r($name);
                //$temp_coll_ids=array();
                $ids='';
                $sql="SELECT e.option_id from `eav_attribute_option_value` as e inner join `eav_attribute_option` as o  on o.option_id=e.option_id where e.value='".$name['collection']."' and o.attribute_id=206";
               $ids= $read->fetchOne($sql);
                $temp_coll_ids[]= $ids;
              }
 			 //echo '<br>collection id-------<br>';
             if(is_array($temp_coll_ids))
             {
                $str_coll_ids='';
                $str_coll_ids=implode(",",$temp_coll_ids);
				$collection_add_sql="UPDATE `aitmanufacturers` SET `collection` = '".$str_coll_ids."' WHERE `aitmanufacturers`.`manufacturer_id` =".$marque;

                $write->query($collection_add_sql);
                echo "manufacturer_id".$marque."--".$str_coll_ids.'<br>';

             }
             echo '<br><<----------------->><br>';
            }
echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>