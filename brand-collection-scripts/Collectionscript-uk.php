<?php 
chdir(dirname(__FILE__));
require '../app/Mage.php';
umask(0);
Mage::app();
Mage::app()->setCurrentStore(3);
$write = Mage::getSingleton('core/resource')->getConnection('core_write');
$collections =  Mage::getModel('catalog/product')->getCollection();

foreach($collections as $collection)
{ 
$product = $collection['entity_id'];
//$product = 162;
$connection = Mage::getSingleton('core/resource')->getConnection('core_read'); 
$storeId = 3;
$ProductModel = Mage::getModel('catalog/product')->load($product);
$collectionID =  $ProductModel->getGammeCollectionNew();
$ManufactureID =  $ProductModel->getManufacturer(); 
if($ManufactureID!="" && $collectionID!="")
{
$mar_pro_check = "SELECT product_ids FROM `marqueproducts` WHERE `marque` != $ManufactureID  AND `collection` != $collectionID  AND find_in_set($product,product_ids)";


$productIds_temp_check=$connection->FetchAll($mar_pro_check);

//echo "<pre>";print_r($productIds_temp_check);die();
        foreach($productIds_temp_check as $data)
        {
            $list = get_filter_list($product,$data["product_ids"]);
           $sql = "UPDATE `marqueproducts` SET product_ids = '.$list.' WHERE `marque` != $ManufactureID  AND `collection` != $collectionID  AND find_in_set($product,product_ids)" ;
            $write->query($sql);
}
}
if($collectionID && $ManufactureID)
{  
        $op = Mage::getModel('catalog/product');
        $query = 'SELECT * FROM `aitmanufacturers_stores` WHERE `manufacturer_id` ='.$collectionID.' AND `store_id` ='.$storeId.'';
        $results = $connection->fetchAll($query);
        $cid =  $results[0]['id']; 
    if($collectionID && $ManufactureID)
{  
        $op = Mage::getModel('catalog/product');
        $query = 'SELECT id FROM `aitmanufacturers_stores` WHERE `manufacturer_id` ='.$collectionID.' AND `store_id` ='.$storeId.'';
        $results = $connection->fetchAll($query);
        $cid =  $results[0]['id']; 
        //echo $cid =  $collectionID; die();
        
    if($cid)
    { 
            $sql = 'SELECT product_ids FROM `marqueproducts` WHERE `marque` = '.$ManufactureID.' AND `collection` = '.$collectionID.'' ;
            $productIds_temp=$connection->FetchAll($sql);
           // echo count($productIds_temp);die();
            if(count($productIds_temp) == 0)
            {
                $sql =  "INSERT INTO `marqueproducts` (marque, collection, product_ids, status) VALUES ($ManufactureID,$collectionID,$product,0)";
                $write->query($sql);
            }
            else
            {
                //echo "<pre>";
                //print_r($productIds_temp);exit;
                $strProductIds = $productIds_temp[0]['product_ids'];
                $arrProductIds = explode(',', $strProductIds);
                if(in_array($product, $arrProductIds))
                {
                    //echo "exist";die();
                }
                else
                {
                
                    $sql = "UPDATE `marqueproducts` SET product_ids = CONCAT(product_ids, ',',$product) WHERE marque = $ManufactureID AND collection = $collectionID" ;
                    $write->query($sql);
                }
            }   	

                

//code for insert into core_url_rewrite table
                
                $ait_man = 'SELECT url_key FROM aitmanufacturers where id='.$cid;
                $result_aitman = $connection->fetchRow($ait_man);
                
                //$_brand = Mage::Helper('aitmanufacturers')->toUrlKey($manufacturename);
                //bhard url
                $ait_store_brand = 'SELECT id FROM `aitmanufacturers_stores` WHERE `manufacturer_id` ='.$ManufactureID.' AND `store_id` ='.$storeId.'';
                $result_aitstore_brand = $connection->fetchRow($ait_store_brand);
                $bid =  $result_aitstore_brand['id'];
                $ait_man_brand = 'SELECT url_key FROM aitmanufacturers where id='.$bid;
                $result_aitman_brand = $connection->fetchRow($ait_man_brand);
                
                //collection url
                $_brand = $result_aitman_brand['url_key'];
                $_collection = $result_aitman['url_key'];

                //echo $_brand.'/'.$_collection.'.html';die();
                //$hj = "'brands/$cid'";
                
               // $hj = "'brands/$cid'";
                $hj = "'$_brand/$_collection.html'";
                $kjh = 'SELECT url_rewrite_id FROM `core_url_rewrite` WHERE store_id ='.$storeId.' AND request_path='.$hj.'';
                $hht = $connection->FetchAll($kjh);
                //echo count($hht);die();
                if((count($hht))==0)
                {
                   
                            $data = Mage::getModel('core/url_rewrite')
                            ->setIsSystem(0)
                            ->setStoreId($storeId)  
                            ->setIdPath('brands-'.date("Y-m-dH:i:s").rand().'-'.$cid)
                            ->setRequestPath($_brand.'/'.$_collection.'.html') 
                            ->setTargetPath('brands/index/view/id/' .$cid)->save();
                   echo "needed to insert- ".$product.PHP_EOL;
                }
                else
                {
                    echo "not needed to insert- ".$product.PHP_EOL;
                }
                echo "done product - ".$product.PHP_EOL;
                /*else
                {
                   
                    
                 $deleteid =  $hht[0]["url_rewrite_id"];
                 $delete = Mage::getModel('core/url_rewrite')->load($deleteid)->delete();
                 
                   $hj = "'brands/$cid'";
                    
                 $data = Mage::getModel('core/url_rewrite')
                            ->setIsSystem(0)
                            ->setStoreId($storeId)  
                            ->setIdPath('brands/'.$cid)
                            ->setRequestPath($_brand.'/'.$_collection.'.html') 
                            ->setTargetPath('brands/index/view/id/' .$cid)->save();
                                     
                 

                }*/
     }
 
}
} 
}
function get_filter_list($input,$list)
{
$array1 = Array($input);
$array2 = explode(',', $list);
$array3 = array_diff($array2, $array1);

$output = implode(',', $array3);
return $output;
}
