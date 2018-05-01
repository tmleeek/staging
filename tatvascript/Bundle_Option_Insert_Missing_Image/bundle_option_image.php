<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
//header('Content-type: text/html; charset=utf-8');

$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$products = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToSelect('*')
    ->addAttributeToSelect('type')
    ->addFieldToFilter('type_id', array('eq' => 'bundle'));


    foreach($products as $bundle_product)
    {

          $id='';
          $id= $bundle_product->getEntityId();

          if($id!='')
          {
            $option_id='';
            $option_id_sql='SELECT option_id FROM `catalog_product_option` WHERE `product_id` = '.$id;
            $option_id=$read->FetchOne($option_id_sql);

            if($option_id!='')
            {
               $option_data=array();
               $option_data_sql='SELECT option_type_id,sku FROM `catalog_product_option_type_value` WHERE `option_id` ='.$option_id;
               $option_data[]=$read->FetchAll($option_data_sql);
            }

           if(is_array($option_data))
           {
              foreach($option_data[0] as $datas)
              {
                  $simple_sku='';   $option_type_id='';

                  $simple_sku=$datas['sku'];
                  $option_type_id=$datas['option_type_id'];
                  if($simple_sku!='')
                  {
                    $simple_product_id =Mage::getModel("catalog/product")->getIdBySku($simple_sku);
                    if($simple_product_id)
                    {
                      $image='';
                      $simple_product=Mage::getModel('catalog/product')->load($simple_product_id);
                      $image=$simple_product->getImage();

                      if($image!='' && $option_type_id!='')
                      {
                         
                           $sql = "INSERT INTO `optionimages_product_option_type_image` (option_type_id,store_id,image)
    		                                          VALUES ('".$option_type_id."', '0','".$image."')";
                           $write->query($sql);
                           $row++;
                           echo $id.'--'.$option_type_id.'--'.$image; echo '<br>';

                      }
                    }
                  }
              }
           }

          }

    }



echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>