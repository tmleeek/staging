<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();
header('Content-type: text/html; charset=utf-8');

$file = 'attribute_values.csv';
$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');
$row = 0;
if (($handle = fopen($file, "r")) !== FALSE)
{
    while (($data = fgetcsv($handle, 1000, ';', '"')) !== FALSE)
    {
        if($row != 0)
        {
            $str_array = array();

            //print_r($data);

            $str = $data[0];  // Admin - Store_id - 0
            //$str = str_replace("'",'"',$str);

            /*$str1 = $data[1];  // Amazon Allemagne FBA - Store_id - 19
            $str2 = $data[2];  // Amazon France - Store_id - 16
            $str3 = $data[3];  // Amazon IT - Store_id - 21
            $str4 = $data[4];  // Amazon UK - Store_id - 17
            $str5 = $data[5];  // Amazon UK - Store_id - 20
            $str6 = $data[6];  // België  (vlaams) - Store_id - 6
            $str7 = $data[7];  // Deutsch - Store_id - 8
            $str8 = $data[8];  // Ebay France - Store_id - 18
            $str9 = $data[9];  // Español - Store_id - 10
            $str10 = $data[10]; // Italiano - Store_id - 7
            $str11 = $data[11]; // Lietuviškai - Store_id - 11
            $str12 = $data[12]; // Nederlands - Store_id - 15
            $str13 = $data[13]; // Pусский - Store_id - 9
            $str14 = $data[14]; // Schweiz (DE) - Store_id - 13
            $str15 = $data[15]; // Svizzera (IT) - Store_id - 14
            $str16 = $data[16]; // Belgique (français) - Store_id - 5
            $str17 = $data[17]; // France - Store_id - 1
            $str18 = $data[18]; // United Kingdom - Store_id - 3
            $str19 = $data[19]; // USA - Store_id - 4
            $str20 = $data[20]; // Suisse (FR) - Store_id - 12*/

            $str_array = array(
                                19 => $data[1], // Amazon Allemagne FBA - Store_id - 19
                                16 => $data[2], // Amazon France - Store_id - 16
                                21 => $data[3], // Amazon IT - Store_id - 21
                                17 => $data[4], // Amazon UK - Store_id - 17
                                20 => $data[5], // Amazon UK - Store_id - 20
                                6 => $data[6],  // België  (vlaams) - Store_id - 6
                                8 => $data[7],  // Deutsch - Store_id - 8
                                18 => $data[8], // Ebay France - Store_id - 18
                                10 => $data[9], // Español - Store_id - 10
                                7 => $data[10], // Italiano - Store_id - 7
                                11 => $data[11], // Lietuviškai - Store_id - 11
                                15 => $data[12], // Nederlands - Store_id - 15
                                9 => $data[13],  // Pусский - Store_id - 9
                                13 => $data[14], // Schweiz (DE) - Store_id - 13
                                14 => $data[15], // Svizzera (IT) - Store_id - 14
                                5 => $data[16],  // Belgique (français) - Store_id - 5
                                1 => $data[17],  // France - Store_id - 1
                                3 => $data[18],  // United Kingdom - Store_id - 3
                                4 => $data[19],  // USA - Store_id - 4
                                12 => $data[20]  //// Suisse (FR) - Store_id - 12
             );

            //print_r($str_array);

            echo $sql = 'SELECT a.option_id from `eav_attribute_option_value` as a inner join `eav_attribute_option` as s on a.`option_id` = s.`option_id` where s.`attribute_id` = 249 and a.`value`= "'.$str.'"';
             echo "<br /><br />";
            $store_admin_data = $read->fetchRow($sql);
              //print_r($store_admin_data);
              //echo "<br /><br />";
            if(empty($store_admin_data))
            {
                echo $sql = 'INSERT INTO `eav_attribute_option` (attribute_id, sort_order) VALUES (249, 0)';
                 echo "<br /><br />";
                $write->query($sql);
                $id =  $write ->lastInsertId();

                echo $sql_store_0 = 'INSERT INTO `eav_attribute_option_value` (option_id, store_id, value)  VALUES ('.$id.', 0, "'.$str.'")';
                 echo "<br /><br />";
			    $write->query($sql_store_0);
            }
            else
            {
                $id = $store_admin_data['option_id'];
            }
           //exit;
            foreach($str_array as $key => $result)
            {
                  //echo "key =".$key;
                  //echo "<br /><br />";
                  //echo "result =".$result;
                  //echo "<br /><br />";
                if(!empty($result))
                {

                    $final_string = str_replace("'",'"',$result);

                    echo $sql_store_0 = 'select * from `eav_attribute_option_value` where `option_id` = '.$id.' and `store_id` = '.$key.' and `value` = "'.$final_string.'"';
                    echo "<br /><br />";
			        $data_result = $read->fetchRow($sql_store_0);
                    //print_r($data_result);
                     //echo "<br /><br />";
                     //exit;
                    if(empty($data_result))
                    {
                        echo $sql_store_1 = 'INSERT INTO `eav_attribute_option_value` (option_id, store_id, value)  VALUES ('.$id.', '.$key.', "'.$final_string.'")';
                        echo "<br /><br />";
			            $write->query($sql_store_1);
                    }
                    else
                    {
                        echo $sql_store_1 = 'UPDATE `eav_attribute_option_value` set `value` = "'.$final_string.'" where `option_id` = '.$id.' and `store_id` = '.$key;
                        echo "<br /><br />";
			            $write->query($sql_store_1);
                    }
                }
            }
        }
        $row++;
    }
}

echo "Total Updated Item:--".$row.'<br>';
echo 'Done';

?>