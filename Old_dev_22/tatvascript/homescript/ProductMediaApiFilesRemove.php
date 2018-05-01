<?php

$id = $_REQUEST['id'];
require_once 'app/Mage.php';
umask(0);
$app = Mage::app('admin');
$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
try {
    $items = $mediaApi->items($id);
    foreach($items as $item) {
        echo $item['file'];
        $mediaApi->remove($id, $item['file']);
    }
    echo "removed";
} catch (Exception $exception){
    var_dump($exception);
    die('Exception Thrown');
}
?>