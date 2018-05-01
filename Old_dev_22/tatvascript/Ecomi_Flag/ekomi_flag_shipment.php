<?php
define('MAGENTO', realpath(dirname(__FILE__)));
include('../app/Mage.php');
Mage::app();



$collection = Mage::getResourceModel('sales/order_shipment_collection')
          ->addAttributeToSelect('increment_id')
          ->addAttributeToSelect('created_at')
          ->addAttributeToSelect('ekomi_flag')
          ->addAttributeToFilter('ekomi_flag', 1)
          ->joinAttribute('order_entity_id', 'order/entity_id', 'order_id', null, 'left')
          ->joinAttribute('order_increment_id', 'order/increment_id', 'order_id', null, 'left');




foreach($collection as $shipment_data)
{
    //$_order = $shipment_data->getOrder();
    $shipment_data = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipment_data->getIncrementId());
    $order_data = Mage::getModel('sales/order')->load($shipment_data->getOrderId());
    $ekomi_store_id = $order_data->getStoreId();
    $ekomi_enable_check = Mage::getStoreConfig('ekomi/ekomiconf/enable_review_email',$ekomi_store_id);

    //print_r($ekomi_enable_check);
    //echo "------------------";
    //print_r($order_data->getData());
    //exit;
    if(!empty($ekomi_enable_check))
    {

        //echo "5757";
        $created_at = explode(" ",$shipment_data->getCreatedAt());
        $todayDate = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));

        $shipment_date =  date("Y-m-d",strtotime($created_at[0]));

        //echo "<br />";
        //print_r($shipment_data->getData());
        //$shipment_date =  date("Y-m-d",strtotime($created_at[0]));
        $date_diff = dateDiff($shipment_date,$todayDate);
        //echo "<br />";
        $ekomi_day_diff = Mage::getStoreConfig('ekomi/ekomiconf/days_after_email_send',$ekomi_store_id);
        //exit;
         /*$date_diff == $ekomi_day_diff &&*/
        if($date_diff == $ekomi_day_diff && $shipment_data->getEkomiFlag() == 1)
        {

            $ekomi_url_generate = Mage::getStoreConfig('ekomi/ekomiconf/generate_ekomi_url',$ekomi_store_id);

            $ekomi_link = "";

            if(!empty($ekomi_url_generate))
            {
               $link = send_order($order_data->getIncrementId(),$ekomi_store_id);
               $ekomi_link = $link['link'];
            }

            /*echo $ekomi_link;
            exit;*/
            $customer_name = $order_data->getCustomerFirstname().' '.$order_data->getCustomerLastname();
            $customer_email = $order_data->getCustomerEmail();
            sendEkomiMail($customer_name,$ekomi_link,$customer_email,$order_data->getStoreId(),$order_data->getIncrementId());


            $shipment_data->setData('ekomi_flag',0);
            $shipment_data->save();
        }
        //}
    }
    //echo "123";
    //exit;
}


function dateDiff($start, $end)
{
    $start_ts = strtotime($start);
    $end_ts = strtotime($end);
    $diff = $end_ts - $start_ts;
    return round($diff / 86400);
}

function sendEkomiMail($customer_name,$link,$customer_email,$store_id,$order_id)
{
    $mailTemplate = Mage::getModel('core/email_template');
    $mailSubject = 'Ekomi Link';

    $customer_name = ucwords(strtolower($customer_name));

    $vars = array();
    $vars['name'] = $customer_name;
    $vars['ekomi_link'] = $link;
    $vars['order_number'] = $order_id;
    $vars['increment_id'] = $order_id;

    $mailTemplate->sendTransactional(
        Mage::getStoreConfig('ekomi/ekomiconf/ekomi_template',$store_id),
        Mage::getStoreConfig('ekomi/ekomiconf/sender_email_identity'),
        $customer_email,
        $customer_name,
        $vars
    );
}

function send_order($order_id,$ekomi_store_id)
{
    $ret = "";

    $ekomi_key = Mage::getStoreConfig('ekomi/ekomiconf/ekomi_key',$ekomi_store_id);

    $api = 'http://api.ekomi.de/v2/wsdl';
    $client = new SoapClient($api, array('exceptions' => 0));
    $send_order = $client->putOrder($ekomi_key, 'cust-1.0.0', utf8_encode($order_id), '');
    $ret = unserialize( utf8_decode( $send_order ) );

    return $ret;
}
?>