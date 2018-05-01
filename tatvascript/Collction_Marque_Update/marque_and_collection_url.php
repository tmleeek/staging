<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
ini_set ( 'memory_limit', '2048M' );


$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read = Mage::getSingleton('core/resource')->getConnection('core_read');

$cvarchartable = 'core_url_rewrite';
$store_id = 0;

/* all manufecture attribute */
$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');
$row = 0;
$row_brand = 0;
foreach ($attribute->getSource()->getAllOptions(true, true) as $option)
{
    if(!$option['value']=='')
    {
        //$marque_value[]= $option['value'];
        $marque_key = $option['label'];
        $marque = $option['value'];

        //$collection_name_data_sql= "SELECT collection from aitmanufacturers as a inner join aitmanufacturers_stores as s on a.manufacturer_id = s.manufacturer_id where s.store_id = 1 and a.manufacturer_id=".$marque;
        $marque_data_sql= "SELECT * from aitmanufacturers as a inner join aitmanufacturers_stores as s on a.manufacturer_id = s.manufacturer_id where a.manufacturer_id=".$marque;
        //echo "<br /><br />";
        $marque_detais = $read->fetchRow($marque_data_sql);


        if(!empty($marque_detais))
        {
            $collection_arr = explode(",",$marque_detais['collection']);
           /* print_r($collection_arr);
             echo "<br /><br />";*/
            if(!empty($marque_detais['collection']))
            {

                foreach($collection_arr as $collection_id)
                {
                    /* Here Key Id is Collection Manufecture_id */
                      //print_r($collection_id);
                        //echo "<br /><br />";
                    if(!empty($collection_id))
                    {

                        $data ='';

                        echo "marque_url_key = ". $marque_url_key = $marque_detais['url_key'];
                        $marque_url_key = $marque_detais['url_key'];
                        echo "<br /><br />";
                        //echo "marque_key_id = ". $marque_key_id = $marque_detais['id'];
                        $marque_key_id = $marque_detais['id'];
                        //echo "<br /><br />";

                        $collection_details = getDetails($collection_id,$read,$write);


                        echo "collection_url_key = ". $collection_url_key= $collection_details['url_key'];
                        echo "<br /><br />";
                        $collection_url_key= $collection_details['url_key'];

                        //echo "collection_key_id = ". $collection_key_id = $collection_details['id'];
                        //echo "<br /><br />";
                        $collection_key_id = $collection_details['id'];

                        //echo "target_path = ". $target_path='brands/index/view/id/'.$collection_key_id;
                        //echo "<br /><br /><br /><br /><br />";
                        $target_path='brands/index/view/id/'.$collection_key_id;


                        /* New Collection Path */
                        if($collection_url_key!='')
                        {
                            $store_id_brand = 1;
                            $request_path_brand= $collection_url_key.'.html';
                            $id_path_brand='brands'.'/'.$collection_key_id;
                            if($id_path_brand && $collection_key_id)
                            {
                                echo $select_brand = "select request_path from ".$cvarchartable." where store_id= '".$store_id_brand."' and (request_path= '".$request_path_brand."' or id_path= '".$id_path_brand."') LIMIT 1";
                                $data_brand = $read->fetchRow($select_brand);
                                echo "<br /><br />";
                            }

                            if($data_brand=='')
                            {
                                echo $insert_brand_url_key = "INSERT INTO ".$cvarchartable." (store_id, id_path, request_path, target_path, is_system)
                                VALUES ('".$store_id_brand."','".$id_path_brand."', '".$request_path_brand."', '".$target_path."', '0');";
                                $write->query($insert_brand_url_key);
                                echo "<br /><br />";
                                //echo $id_path_brand.'<br><br>';
                                $row_brand++;
                            }
                            else
                            {
                                if($request_path_brand != $data_brand['request_path'])
                                {
                                    echo $upate_brand_url_key = "Update ".$cvarchartable." set request_path ='".$request_path_brand."' where id_path = '".$id_path_brand."'";
                                    $write->query($upate_brand_url_key);
                                    echo "<br /><br />";
                                    //echo $id_path_brand.'<br><br>';
                                    $row_brand++;
                                }

                            }
                        }

                        /* New marque Collection Path */
                        if($marque_url_key!='' && $collection_url_key!='')
                        {

                            $request_path = $marque_url_key.'/'.$collection_url_key.'.html';
                            $id_path='brands/collection/'.$marque_key_id.'/'.$collection_key_id;
                            if($id_path && $collection_key_id)
                            {
                                echo $select = "select request_path from ".$cvarchartable." where store_id= '".$store_id."' and (request_path= '".$request_path."' or id_path= '".$id_path."') LIMIT 1";
                                $data = $read->fetchRow($select);
                                echo "<br /><br />";
                            }

                            if($data=='')
                            {
                                echo $insert_url_key = "INSERT INTO ".$cvarchartable." (store_id, id_path, request_path, target_path, is_system)
                                VALUES ('".$store_id."','".$id_path."', '".$request_path."', '".$target_path."', '0');";
                                $write->query($insert_url_key);
                                echo "<br /><br />";
                                //echo $id_path.'<br><br>';
                                $row++;
                            }
                            else
                            {
                                if($request_path != $data['request_path'])
                                {
                                    echo $upate_url_key = "Update ".$cvarchartable." set request_path ='".$request_path."' where id_path = '".$id_path."'";
                                    $write->query($upate_url_key);
                                    echo "<br /><br />";
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function getDetails($ids,$read,$write)
{
    $collection_data_sql= "SELECT * from aitmanufacturers where manufacturer_id=".$ids;
    //echo "<br />";
    $collection_details = $read->fetchRow($collection_data_sql);

    return $collection_details;
}

echo "Total Inserted Item:--".$row.'<br>';
echo "Total Inserted Brand Item:--".$row_brand.'<br>';

echo 'Done';
?>