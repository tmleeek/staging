<?php

require 'app/Mage.php';
Mage::app();

$orderId = 100042073;
$order = Mage::getModel('sales/order')->load($orderId);
$order->cancel()->save();
?>