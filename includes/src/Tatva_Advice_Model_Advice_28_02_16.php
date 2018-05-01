<?php

class Tatva_Advice_Model_Advice extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advice/advice');
    }

    public function getBundleSimpleImages($id)
    {
      $check=array();
      $write = Mage::getSingleton("core/resource")->getConnection("core_write");
      $read= Mage::getSingleton('core/resource')->getConnection('core_read');
      if($id!='')
          {
            $option_id='';
            $option_id_sql='SELECT option_id FROM `catalog_product_option` WHERE `product_id` = '.$id;
            $option_id=$read->FetchOne($option_id_sql);

            if($option_id!='')
            {
               $option_data=array();
               $option_data_sql='SELECT option_type_id FROM `catalog_product_option_type_value` WHERE `option_id` ='.$option_id;
               $option_data[]=$read->FetchAll($option_data_sql);
            }

           if(is_array($option_data))
           {
              foreach($option_data[0] as $datas)
              {
                   $option_type_id='';
                   $option_type_id=$datas['option_type_id'];
                   if($option_type_id!='')
                   {


                         $sql_check_entry='SELECT o.image,t.title FROM optionimages_product_option_type_image o JOIN catalog_product_option_type_title t ON o.option_type_id=t.option_type_id WHERE t.option_type_id ='.$option_type_id;
                         $check[]=$read->FetchAll($sql_check_entry);

                      }
                    }
                  }
              }
         //echo "<pre>"; print_r($check); 
         return $check;

    }

    public function getCatlogIfoldUrlExist($request_data)
    {
     $result='';
     $store_id = Mage::app()->getStore()->getId();
     $ids=explode('/',$request_data);

	$totalcount = count($ids);
     $lengo_id=''; 
	 $product_id='';

	 if($totalcount > 1)
     	$lengo_id=$ids[$totalcount-1];

    //echo $lengo_id;
      if($lengo_id!='')
       {
          $collection = Mage::getModel('catalog/product')->getCollection();
          $collection->setStoreId($store_id);
          //$collection->addStoreFilter($store_id);
          $collection->addAttributeToSelect('lengow_id');
          $collection->addAttributeToSelect('url_key');
          $collection->addFieldToFilter('lengow_id', $lengo_id);

         foreach($collection as $model_data)
         {
            $result=$model_data['url_key'].'.html';   

         }
       }
	
      return $result;
    }
}