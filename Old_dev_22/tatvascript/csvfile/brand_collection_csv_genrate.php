<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

//echo "hi"; exit;
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$data='';
$val=array();

$filename = "brand_collection_csv_genrate.csv";
$fp = fopen($filename, "w");
$line = ""; $qty_value=0;
$all_brand_value_ids_array=array();



  /* get all attribute ids */



    $line.= '"' . str_replace('"', '""', 'Brand Name') . '"'.','.'"' . str_replace('"', '""', 'Collection Name') . '"'.','.'"' . str_replace('"', '""', 'Collection Url').'"';
    $line.= "\n";

   /* collection all  */
   $list_colls= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();

   foreach($list_colls as $datas)
   {

          $temp_gemma_collection_array=array();   $gamme_collection='';

          $gamme_collection= $datas['collection'];
          $temp_gemma_collection_array=explode(',',$gamme_collection);
          //echo "<pre>"; print_r($temp_gemma_collection_array);
           if(is_array($temp_gemma_collection_array))
           {
            foreach($temp_gemma_collection_array as $gamme_colls)
            {
              $temp_url='';
              $marque_name='';
              $collection_name='';

              $temp_url=Mage::getBaseUrl().getlistgammeUrl($gamme_colls,$datas['manufacturer_id']);

             echo  $marque_name=getOptiondata('manufacturer',$datas['manufacturer_id']); echo '<br>';
             echo $collection_name= getOptiondata('gamme_collection_new',$gamme_colls);   echo '<br>';

              if($collection_name!='')
              {
                  $line.= '"' . str_replace('"', '""', $marque_name) . '"'.','.'"' . str_replace('"', '""', $collection_name) . '"'.','.'"' . str_replace('"', '""', $temp_url).'"';
               $line.= "\n";
               $row++;
              }

            }
           }

   }




 function getlistgammeUrl($gamme_id, $brand_id)
 {
    $result_url='';
    $gamme_url_key='';  $brand_url_key='';

    $gamme_url_key = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($gamme_id)->getUrlKey();
    $brand_url_key = Mage::getModel('aitmanufacturers/aitmanufacturers')->loadByManufacturer($brand_id)->getUrlKey();

    if(($gamme_url_key!='') && ($brand_url_key!=''))
    {
       $result_url=$brand_url_key.'/'.$gamme_url_key.'.html';
    }
    elseif(($gamme_url_key!=''))
    {
      $result_url=$gamme_url_key.'.html';
    }

    return  $result_url;
 }

function getOptiondata($attribute_code,$val)
{
      $str='';
      $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute_code);
      foreach ($attribute->getSource()->getAllOptions(true, true) as $instance) {
                if($instance['value']==$val)
                {
                    $str=$instance['label'];
                }
            }
      return $str;
}


fputs($fp, $line);
fclose($fp);
echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>