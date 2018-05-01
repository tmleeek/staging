<?php

define('MAGENTO', realpath(dirname(__FILE__)));

require_once '../../app/Mage.php';
//echo date('Y-m-d h:i:s');exit;
//Varien_Profiler::enable();

//Mage::setIsDeveloperMode(true);

ini_set('display_errors', 1);

Mage::app();
$fromDate='2010-3-01 00:00:00';
$toDate='2015-3-08 00:00:00';

/* Format our dates */
//echo $fromDate = date('Y-m-d H:i:s', strtotime($fromDate));
//echo $toDate = date('Y-m-d H:i:s', strtotime($toDate));
//exit;

$orders = Mage::getModel('sales/order')->getCollection()
->addFieldToSelect('*')
->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
->addFieldToFilter('store_id', array('gt' => '14'))
//->addFieldToFilter('status', 'complete')
->setOrder('created_at', 'desc');

//SELECT * FROM `sales_flat_order` WHERE `created_at` <= '2018-03-09 00:00:00' and `created_at` => '2018-03-01 00:00:00' LIMIT 0 , 30

//echo $orders->getSelect();
//exit;
$shipping_data = array();
foreach ($orders as $order)
{
    $shipping_method = $order->getShippingMethod();
    $shipping_desc = $order->getShippingDescription();

    if(!in_array($shipping_method, $shipping_data['shipping_method']))
        $shipping_data['shipping_method'][] = $shipping_method;

    if(!in_array($shipping_desc, $shipping_data['shipping_desc']))
        $shipping_data['shipping_desc'][] = $shipping_desc;
}
echo "<pre>";
print_r($shipping_data);
echo "</pre>";

?>
