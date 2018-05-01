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

               $_order=$shipment_data->getOrder();
			   $shipment_data = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipment_data->getIncrementId());

               $shipping_method = explode("_",$_order->getShippingMethod());
               $created_at = explode(" ",$shipment_data->getCreatedAt());
               $todayDate = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));

               $shipment_date =  date("Y-m-d",strtotime($created_at[0]));


         $shipment_date =  date("Y-m-d",strtotime($created_at[0])); 
         if($shipment_date == '2013-12-13' && $shipping_method[0] == 'gls_gls')
        {

        }
       else
       {
            $date_diff = dateDiff($shipment_date,$todayDate);

               //$date_diff = 7;

                 if($date_diff == 7 && $shipment_data->getEkomiFlag() == 1)
                 {
                     
                     $order_data = Mage::getModel('sales/order')->load($shipment_data->getOrderId());
					 
					 $link = send_order($order_data->getIncrementId()); 
                     $link = $link['link'];
					 
                     $customer_name = $order_data->getCustomerFirstname().' '.$order_data->getCustomerLastname();
                     $customer_email = $order_data->getCustomerEmail();
                     sendEkomiMail($customer_name,$link,$customer_email,$order_data->getStoreId(),$order_data->getIncrementId());


                    $shipment_data->setData('ekomi_flag',0);
					$shipment_data->save();
                 }
       }
     }



    function dateDiff($start, $end) {
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
    						Mage::getStoreConfig('sales_email/order/ekomi_template',$store_id),
                            Mage::getStoreConfig('sales_email/order/sender_email_identity'),
    						$customer_email,
                            $customer_name,
                            $vars

                     );
	   
 
	   
       
    }
	
	function send_order( $order_id ) {

	$api          = 'http://api.ekomi.de/v2/wsdl';

	$client       = new SoapClient($api, array('exceptions' => 0)); 
	$send_order   = $client->putOrder('25224|wlD6aSEruWeCRF4SXaw3RGWec', 'cust-1.0.0', utf8_encode($order_id), '');
	$ret          = unserialize( utf8_decode( $send_order ) );

	return $ret;

   }
?>
