<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
ini_set ( 'memory_limit', '2048M' );


$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
/*
$collection=Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();
$marque_ids=array();
$gamme_collection_array=array();
$product_ids='';
echo $collection->getSelect();exit;
*/
/* empty existing table */
   $empty='TRUNCATE TABLE `marqueproducts`';
   $write->query($empty);
                $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');
            foreach ($attribute->getSource()->getAllOptions(true, true) as $option){
              if(!$option['value']=='')
               {
                $marque_value[]= $option['value'];
               }
             }
			 $row = 0;
			 foreach($marque_value as $marque )
			 {
			 	  $collection_name_data_sql= "SELECT collection from aitmanufacturers as a inner join aitmanufacturers_stores as s on a.manufacturer_id = s.manufacturer_id where s.store_id = 1 and a.manufacturer_id=".$marque;
				  $collection= $read->fetchOne($collection_name_data_sql);  
				  $collection_arr = explode(",",$collection);
				  foreach($collection_arr as $collection_id)
				  {
				  	 $product_ids= Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductFilter($marque,$collection_id); 
					 $sql="INSERT INTO `marqueproducts` SET `marque` = '".$marque."' , collection='".$collection_id."' , product_ids='".$product_ids."'";
	                  $write->query($sql);
					  
				  }
				 echo $marque.'==='.$row.'<br>'; 
				$row++;
			 }
   exit;
  /* manufacture id get */
 foreach($collection as $colls)
  {
    /* collection filter  */

        $marque=$colls['manufacturer_id'];
        $gamme_collection= $colls['collection']; 

        if($marque!='' && $gamme_collection!='')
        {
        $gamme_collection_array= explode(",",$gamme_collection);

        if(is_array($gamme_collection_array))
        {
            foreach($gamme_collection_array  as $gamme_array)
            {
              $temp='';
              $product_ids= Mage::getModel('aitmanufacturers/aitmanufacturers')->getProductFilter($marque,$gamme_array);
              $temp=$product_ids;
			  
			  if($temp!='')
	             {echo $marque.'=='.$gamme_array.'<br>';
	             $sql="INSERT INTO `marqueproducts` SET `marque` = '".$marque."' , collection='".$gamme_array."' , product_ids='".$temp."'";
	             $write->query($sql);
	             $row++;
	             }
            }
			
            

        }
       }
    } 



echo "Total Inserted Item:--".$row.'<br>';
echo 'Done';



?>