<?php


/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Marketplace_Orders_Update extends Mage_Core_Model_Abstract
{
    public function UpdateOrder()
    {
        $marketsote_ids = Mage::getStoreConfig('marketplace_shipping/marketplace_shipping/store_select');
        $market_place_store_id = explode(',',$marketsote_ids);

        $write = Mage::getSingleton("core/resource")->getConnection("core_write");
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');

        //$fromDate = date('Y-m-d H:i:s');
        $toDate= date('Y-m-d H:i:s', strtotime('-1 hour'));

        $orders = Mage::getModel('sales/order')->getCollection()
        ->addFieldToSelect('*')
        ->addAttributeToFilter('created_at', array('from'=>$toDate, 'to'=>now()))
        ->addAttributeToFilter( 'status' , array( 'in'=> array("processing","pending") ) )
        ->addFieldToFilter('store_id',   array("in" => array($market_place_store_id)))
        ->setOrder('created_at', 'desc');



        $market_place_amazone_shipping_sql = 'SELECT shipping_code_amazon FROM `tatva_shipping_marketmethod` group by shipping_code_amazon';
        $market_place_amazone_shipping = $read->FetchAll($market_place_amazone_shipping_sql);

        $market_place_ebay_shipping_sql = 'SELECT shipping_code_ebay FROM `tatva_shipping_marketmethod` group by shipping_code_ebay';
        $market_place_ebay_shipping = $read->FetchAll($market_place_ebay_shipping_sql);

        //print_r($market_place_shipping);
        //exit;

        $shipping_data = array();
        foreach ($orders as $order)
        {
            $weight = $order->getWeight();
            $order_total = $order->getGrandTotal();
            $order_shipping_country = $order->getShippingAddress()->getCountryId();
            $shipping_method = $order->getShippingMethod();
            $shipping_desc = $order->getShippingDescription();
            $final_market_code = '';
            foreach($market_place_amazone_shipping as $amazone_shipping_method)
            {
                $final_market_code = '';
                $code = $amazone_shipping_method['shipping_code_amazon'];
                if (strpos($shipping_desc, $code) !== false)
                {
                   $final_market_code = $code;
                }
            }

            if(empty($final_market_code))
            {
                foreach($market_place_ebay_shipping as $ebay_shipping_method)
                {
                    $final_market_code = '';
                    $code = $ebay_shipping_method['shipping_code_ebay'];
                    if (strpos($shipping_desc, $code) !== false)
                    {
                        $final_market_code = $code;
                    }
                }
            }


            if(!empty($final_market_code))
            {
                $sql = 'SELECT market_shipping_code FROM `tatva_shipping_marketmethod` WHERE `shipping_code_amazon` = "'.$final_market_code.'" or shipping_code_ebay = "'.$final_market_code.'"
                and `market_weight_from` <= '.$weight.' and `market_weight_to` >= '.$weight.'
                and `market_ordertotal_from` <= '.$order_total.' and `market_ordertotal_to` >= '.$order_total.' and countries_ids like"%'.$order_shipping_country.'%"';
                $shipping_change = $read->FetchOne($sql);

                if (strpos($shipping_desc, $final_market_code) !== false)
                {
                    if(!empty($shipping_change))
                    {
                        /* echo $order->getId();
                        echo "<br />";
                        echo  $shipping_change;
                        exit;*/

                        mage::helper('Orderpreparation/ShippingMethods')->changeForOrder($order->getId(), $shipping_change);

                        $to = "sagarshahitprofessional@gmail.com; julien.debecker@az-boutique.fr";
                        $subject = "MarketPlace Order Update";

                        $message = "Order Id = ".$order->getId()."<br /><br />";
                        $message .= "Order Toral = ".$order_total."<br /><br />";
                        $message .= "Order Shipping Country = ".$order_shipping_country."<br /><br />";
                        $message .= "Shipping Desc = ".$shipping_desc."<br /><br />";
                        $message .= "New Shipping = ".$shipping_change. "<br /><br />";

                        // Always set content-type when sending HTML email
                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                        // More headers
                        $headers .= 'From: <sagarshahitprofessional@gmail.com>' . "\r\n";

                        // echo $message;
                        mail($to,$subject,$message,$headers);
                        //exit;
                    }
                }
            }
        }

    }
}

?>