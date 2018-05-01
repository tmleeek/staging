<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
ini_set ( 'memory_limit', '2048M' );


$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$cvarchartable='core_url_rewrite';
$store_id=Mage :: app()->getStore();

            /* all manufecture attribute */
           $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');
            foreach ($attribute->getSource()->getAllOptions(true, true) as $option){
              if(!$option['value']=='')
               {
                $marque_value[]= $option['value'];
               }
             }
			 $row = 0;
             //echo "<pre>"; print_r($marque_value); exit;
			 foreach($marque_value as $marque)
			 {
			 	  $collection_name_data_sql= "SELECT collection from aitmanufacturers as a inner join aitmanufacturers_stores as s on a.manufacturer_id = s.manufacturer_id where s.store_id = 1 and a.manufacturer_id=".$marque;
				  $collection= $read->fetchOne($collection_name_data_sql);
				  $collection_arr = explode(",",$collection);
				  foreach($collection_arr as $collection_id)
				  {
				      /* Here Key Id is Collection Manufecture_id */
                      $marque_key='';  $collection_key='';  $data='';

                     $marque_key= getProductKey($marque);
                     $collection_key= getProductKey($collection_id);
                      $key_id=getKeyId($collection_id);
                      $marque_key_id=getKeyId($marque);
                      $target_path='brands/index/view/id/'.$key_id;

                      /* New marque Collection Path */
                      if($marque_key!='' && $collection_key!='')
                      {
                         $request_path= $marque_key.'/'.$collection_key.'.html';
                          $id_path='brands/collection/'.$marque_key_id.'/'.$key_id;
                          if($id_path && $key_id)
                          {
                            $select = "select request_path from ".$cvarchartable." where id_path= '".$id_path."' LIMIT 1";
                            $data=$read->FetchOne($select);
                          }

                         if($data=='')
                         {
    					  $insert_url_key = "INSERT INTO ".$cvarchartable." (store_id, id_path, request_path, target_path, is_system)
        		                                          VALUES ('".$store_id."','".$id_path."', '".$request_path."', '".$target_path."', '0');";
    	                  $write->query($insert_url_key);
                          echo $id_path.'<br>';  $row++;
                         }
                      }

				  }

			 }


   function getProductKey($id)
   {
     $key='';
     $list_colls= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
     $list_colls->addFieldToFilter('manufacturer_id',$id);
     foreach($list_colls as $list)
     {
       $key=$list->getUrlKey();
     }
     return $key;
   }

   function getKeyId($ids)
   {
     $keys='';
     $list_colls= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
     $list_colls->addFieldToFilter('manufacturer_id',$ids);
     foreach($list_colls as $list)
     {
       $keys=$list->getId();
     }
     return $keys;
   }

echo "Total Inserted Item:--".$row.'<br>';
echo 'Done';



?>