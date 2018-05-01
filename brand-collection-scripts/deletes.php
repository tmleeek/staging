<?php 
chdir(dirname(__FILE__));
require '../app/Mage.php';
umask(0);
Mage::app();
Mage::app()->setCurrentStore(1);
$deleteflag = 0;
$saveflag = 0;
$connection = Mage::getSingleton('core/resource')->getConnection('core_read'); 
$write = Mage::getSingleton('core/resource')->getConnection('core_write');
$collections =  Mage::getModel('catalog/product')->getCollection();
foreach($collections as $collection)
{
    $PKID =  $collection["entity_id"];
    //$PKID =  7676;
    $storeId = 3;
    $ProductModel = Mage::getModel('catalog/product')->load($PKID);
    $collectionID =  $ProductModel->getGammeCollectionNew();
    $ManufactureID =  $ProductModel->getManufacturer();
    $op = Mage::getModel('catalog/product');
    if($collectionID && $ManufactureID)
    {
        $query = 'SELECT * FROM `aitmanufacturers_stores` WHERE `manufacturer_id` ='.$collectionID.' AND `store_id` ='.$storeId.''; 
        $results = $connection->fetchAll($query);
        $cid =  $results[0]['id'];
        if($cid)
        {
            /*$oUrlRewriteCollection = Mage::getModel('core/url_rewrite')
                                     ->getCollection()
                                     ->addFieldToFilter('id_path', 'brands/'.$cid)
                                     //->addFieldToFilter('target_path',"'brands/index/view/id/$collectionID'")
                                     ->addFieldToFilter('store_id', $storeId);*/
                    $ait_man = 'SELECT url_key FROM aitmanufacturers where id='.$cid;
                    $result_aitman = $connection->fetchRow($ait_man);
                    $ait_store_brand = 'SELECT id FROM `aitmanufacturers_stores` WHERE `manufacturer_id` ='.$ManufactureID.' AND `store_id` ='.$storeId.'';
                    $result_aitstore_brand = $connection->fetchRow($ait_store_brand);
                    $bid =  $result_aitstore_brand['id'];
                    $ait_man_brand = 'SELECT url_key FROM aitmanufacturers where id='.$bid;
                    $result_aitman_brand = $connection->fetchRow($ait_man_brand);

                    //collection url
           $_brand = $result_aitman_brand['url_key'];
           $_collection = $result_aitman['url_key'];
           $target_path = "brands/index/view/id/$cid";
           $ID_PATH = "'brands/$cid'";
           $request_path = "'$_brand".'/'."$_collection.html'";
           //$kjh = 'SELECT * FROM `core_url_rewrite` WHERE target_path="'.$target_path.'"' ;
           $kjh = 'SELECT * FROM `core_url_rewrite` WHERE request_path='.$request_path.'' ;
           //$kjh = 'SELECT * FROM `core_url_rewrite` WHERE id_path='.$ID_PATH.'';
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
