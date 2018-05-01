<?php
require_once 'app/Mage.php';

umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$userModel = Mage::getModel('admin/user');
$userModel->setUserId(0);

$file = 'french_5.csv';
$store_id_fr = 1;
$csv = new Varien_File_Csv();
$data = $csv->getData($file);

foreach($data as $alldata){
    $datasku=$alldata[0];
    $datametakeyword=$alldata[2];
    $dataean13=$alldata[1]; 
    
try {
$get_item = Mage::getModel('catalog/product')->setStoreId($store_id_fr)->loadByAttribute('sku', $datasku);
if ($get_item) {
//$get_item->setMetaKeyword($datametakeyword)->save();
    $get_item->setData('meta_keyword', $datametakeyword)->getResource()->saveAttribute($get_item, 'meta_keyword');
if($dataean13!=""){
//$get_item->setEan13($dataean13)->save();
    $get_item->setData('ean13', $dataean13)->getResource()->saveAttribute($get_item, 'ean13');
}
echo $get_item->getSku()."updated successful".PHP_EOL;
} else {
echo "item not found";
}
} catch (Exception $e) {
echo "Cannot retrieve products from Magento: ".$e->getMessage()."<br>";
return;
}
}
?>