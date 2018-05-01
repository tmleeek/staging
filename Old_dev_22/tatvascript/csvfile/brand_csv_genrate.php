<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
//header('Content-type: text/html; charset=utf-8');

//echo "hi"; exit;
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
$data='';
$val=array();

$filename = "brand_csv_genrate.csv";
$fp = fopen($filename, "w");
$line = ""; $qty_value=0;
$all_brand_value_ids_array=array();



  /* get all attribute ids */
   $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');
      foreach ($attribute->getSource()->getAllOptions(true, true) as $instance) {
            $all_brand_value_ids_array[] = $instance['value'];
            }
      // echo "<pre>"; print_r($all_brand_value_ids_array);


    $line.= '"' . str_replace('"', '""', 'Brand Name') . '"'.','.'"' . str_replace('"', '""', 'Brand Url') . '"';
    $line.= "\n";
   /* collection all  */
   $list_colls= Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection();

   foreach($list_colls as $datas)
   {
       if (in_array($datas['manufacturer_id'], $all_brand_value_ids_array))
       {
         $temp_url='';
         $temp_url=Mage::getBaseUrl().$datas['url_key'].'.html';
         $title='';
         $title= getOptiondata('manufacturer',$datas['manufacturer_id']);

         $line.= '"' . str_replace('"', '""', $title) . '"'.','.'"' . str_replace('"', '""', $temp_url) . '"';
         $line.= "\n";
         $row++;
       }

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




//echo "<pre>"; print_r($line);
fputs($fp, $line);
fclose($fp);
echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>