<?php

define('MAGENTO', realpath(dirname(__FILE__)));

require_once 'app/Mage.php';

ini_set('display_errors', 1);

Mage::app();

$write = Mage::getSingleton("core/resource")->getConnection("core_write");
$read= Mage::getSingleton('core/resource')->getConnection('core_read');

$fromDate='2013-1-01 00:00:00';
$toDate='2018-1-01 00:00:00';

$marketsote_ids = Mage::getStoreConfig('marketplace_shipping/marketplace_shipping/store_select');
$market_place_store_id = explode(',',$marketsote_ids);

$orders = Mage::getModel('sales/order')->getCollection()
->addFieldToSelect('*')
->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
//->addFieldToFilter('status', 'complete')
->addFieldToFilter('store_id',   array("in" => array($market_place_store_id)))
->setOrder('created_at', 'desc');

//echo $orders->getSelect();
//exit;

$market_place_shipping = Mage::getResourceModel('tatvashipping/marketmethod_collection')
                        ->addFieldToSelect('shipping_code_amazon');
$market_place_shipping->getSelect()->group('shipping_code_amazon');

$shipping_data = array();
foreach ($orders as $order)
{
    $weight = $order->getWeight();
    $order_total = $order->getGrandTotal();
    $order_shipping_country = $order->getShippingAddress()->getCountryId();
    $shipping_method = $order->getShippingMethod();
    $shipping_desc = $order->getShippingDescription();
    foreach($market_place_shipping as $market_place_shipping_method)
    {
        $code = $market_place_shipping_method['shipping_code_amazon'];
        if (strpos($shipping_desc, $code) !== false)
        {
           $final_amazon_code = $code;
        }
    }

    $sql = 'SELECT market_shipping_code FROM `tatva_shipping_marketmethod` WHERE `shipping_code_amazon` = "'.$final_amazon_code.'"
    and `market_weight_from` <= '.$weight.' and `market_weight_to` >= '.$weight.'
    and `market_ordertotal_from` <= '.$order_total.' and `market_ordertotal_to` >= '.$order_total.' and countries_ids like"%'.$order_shipping_country.'%"';
    $shipping_change = $read->FetchOne($sql);

    echo $shipping_desc;
    exit;
    if (strpos($shipping_desc, $final_amazon_code) !== false)
    {
        if(!empty($shipping_change))
        {
            /* echo $order->getId();
        echo "<br />";
        echo  $shipping_change;
        exit;*/
            mage::helper('Orderpreparation/ShippingMethods')->changeForOrder($order->getId(), $shipping_change);
            echo $order->getId();
        echo "<br />";
        echo  $shipping_change;
        exit;
        }
    }

    /*if(!in_array($shipping_method, $shipping_data['shipping_method']))
        $shipping_data['shipping_method'][] = $shipping_method;

    if(!in_array($shipping_desc, $shipping_data['shipping_desc']))
        $shipping_data['shipping_desc'][] = $shipping_desc;*/

}
echo "<pre>";
print_r($final_shipping);
echo "</pre>";

?>
