<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = Mage::getBaseDir() . '/tatvascript/material.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;

if (($handle = fopen($file, "r")) !== FALSE) {

      while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            //echo "<pre>"; print_r($data);

  			// Load by SKU
                   $str_new='';
                   $temp=array();  $material_id=array();
			       $str = $data[1];
                   $str = str_replace(";",",",$str);
                   $temp[]=explode(',',$str);

                   //echo "<pre>"; print_r($temp); exit;
                    foreach($temp as $temps)
                    {
                       foreach($temps as $temp_data)
                       {
                        $temp_data = str_replace("'",'"',$temp_data);
                        $sql1 = "SELECT option_id FROM eav_attribute_option_value where value='".$temp_data."' LIMIT 1";
				        $material_id[] = $read->FetchOne($sql1);
                       }
                    }
                    //echo "<pre>"; print_r($material_id); exit;
                   if(is_array($material_id))
                   {
                      $str_new=implode(',',$material_id);
                   }
                   //echo  $str_new;
                   $str_data='';
                   $str_new = str_replace("'",'"',$str_new);
                   $str_data = str_replace("'",'"',$data[0]);
                   $str_new = str_replace("/",'//',$str_new);

				   echo $sql = "INSERT INTO `tatva_material_temp` SET old_material = '".$str_data."', material = '".$str_new."'";  echo '<br>';
                   $write->query($sql);
                   $row++;
              }
	  }



echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>