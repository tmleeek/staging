<?php

/*$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));


$my_file = $_SERVER['DOCUMENT_ROOT'].'/lengow/file.txt';

$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
$data = 'New data line 1';
fwrite($handle, $data);
$new_data = "\n".'New data line 2';
fwrite($handle, $new_data);

$my_file = 'file.txt';
$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
//write some data here
fclose($handle);*/

define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();

$orderIncrementId = 400002355;

$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

//$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();

$order->setBaseDiscountCanceled(0);
$order->setBaseShippingCanceled(0);
$order->setBaseSubtotalCanceled(0);
$order->setBaseTaxCanceled(0);
$order->setBaseTotalCanceled(0);
$order->setDiscountCanceled(0);
$order->setShippingCanceled(0);
$order->setSubtotalCanceled(0);
$order->setTaxCanceled(0);
$order->setTotalCanceled(0);

foreach($order->getAllItems() as $item){
    $item->setQtyCanceled(0);
    $item->setTaxCanceled(0);
    $item->setHiddenTaxCanceled(0);
    $item->save();
}

$order->save();

echo "---------------------------------------------------------------------------------";

echo "<pre>";
print_r($order->getData());
echo "</pre>";
?>