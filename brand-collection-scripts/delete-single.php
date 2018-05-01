<?php 
chdir(dirname(__FILE__));
require '../app/Mage.php';
umask(0);
Mage::app();
$deleteflag = 0;
$saveflag = 0;
$connection = Mage::getSingleton('core/resource')->getConnection('core_read'); 
$write = Mage::getSingleton('core/resource')->getConnection('core_write');
$collections =  Mage::getModel('catalog/product')->getCollection();
foreach($collections as $collection)
{
    $PKID =  $collection["entity_id"];
    //$PKID =  6386;
    $storeId = 3;
    $ProductModel = Mage::getModel('catalog/product')->load($PKID);
    $collectionID =  $ProductModel->getGammeCollectionNew();
    $ManufactureID =  $ProductModel->getManufacturer();
    $op = Mage::getModel('catalog/product');
    if($collectionID)
    {
        $query = 'SELECT * FROM `aitmanufacturers_stores` WHERE `manufacturer_id` ='.$collectionID.' AND `store_id` =4'; 
        $results = $connection->fetchAll($query);
        $cid =  $results[0]['id'];
        if($cid)
        {
            $oUrlRewriteCollection = Mage::getModel('core/url_rewrite')
                                     ->getCollection()
                                     ->addFieldToFilter('id_path', 'brands/'.$cid)
                                     ->addFieldToFilter('store_id', $storeId);
           //echo $ID_PATH = "'brands/index/view/id/$collectionID'";die();
            
           $ID_PATH = "'brands/$cid'";die();
           // echo $ID_PATH = "'brands/$collectionID'";die();
           $kjh = 'SELECT * FROM `core_url_rewrite` WHERE store_id ='.$storeId.' AND id_path='.$ID_PATH.'' ;
           $hht = $connection->FetchAll($kjh);
           $IDDelete = $hht[0]['url_rewrite_id'];
           $model = Mage::getModel('core/url_rewrite');
           if($model->setId($IDDelete)->delete())
           {

                echo "deleted".$PKID.PHP_EOL;
           }
        }
    }
}
