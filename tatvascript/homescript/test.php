<?php 
/*define('MAGENTO', realpath(dirname(__FILE__)));
include('app/Mage.php');
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

    if($ekomi_enable_check != 0)
    {
        $created_at = explode(" ",$shipment_data->getCreatedAt());
        $todayDate = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
        $shipment_date =  date("Y-m-d",strtotime($created_at[0]));
        $date_diff = dateDiff($shipment_date,$todayDate);
        $ekomi_day_diff = Mage::getStoreConfig('ekomi/ekomiconf/days_after_email_send',$ekomi_store_id);

        if($date_diff > $ekomi_day_diff && $shipment_data->getEkomiFlag() == 1)
        {
            echo $shipment_date;
            echo "<br />";
            echo $date_diff;
            echo "<br />";
            echo $ekomi_day_diff;
            echo "<br />";
            $ekomi_url_generate = Mage::getStoreConfig('ekomi/ekomiconf/generate_ekomi_url',$ekomi_store_id);

            $ekomi_link = "";

            if(!empty($ekomi_url_generate))
            {
               $link = send_order($order_data->getIncrementId(),$ekomi_store_id);
               $ekomi_link = $link['link'];
            }

            $customer_name = $order_data->getCustomerFirstname().' '.$order_data->getCustomerLastname();
            $customer_email = $order_data->getCustomerEmail();

            sendEkomiMail($customer_name,$ekomi_link,$customer_email,$order_data->getStoreId(),$order_data->getIncrementId());

            echo "<br /><br />";
            echo "--------------------------------------- email send ----------------------------------------------------";
            echo "<br /><br /><br /><br />";

            $shipment_data->setData('ekomi_flag',0);
            $shipment_data->save();
        }
        //}
    }
    else
    {
        //$shipment_data->setData('ekomi_flag',0);
        //$shipment_data->save();
    }

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
    //echo $customer_name;
//    echo "<br />";
//    echo $link;
//    echo "<br />";
//    echo $customer_email;
//    echo "<br />";
//    echo $order_id;
//    echo "<br />";
//    echo Mage::getStoreConfig('ekomi/ekomiconf/ekomi_template',$store_id);
//    echo "<br />";
//    echo Mage::getStoreConfig('ekomi/ekomiconf/sender_email_identity');
//    echo "<br />";

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
}*/
?>