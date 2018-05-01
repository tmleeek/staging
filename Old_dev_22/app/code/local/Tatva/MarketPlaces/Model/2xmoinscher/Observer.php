<?php
/**
 * created : 01 oct. 2009
 *
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Tatva_MarketPlaces
 * @author alay
 * @copyright SQLI - 2009 - http://www.tatva.com
 */

/**
 * 
 * @package Tatva_MarketPlaces
 */
class Tatva_MarketPlaces_Model_2xmoinscher_Observer {
	
	public function cancelOrder($observer) {
		
		$order =  $observer->getEvent()->getOrder();
		if($order && $order->getState() === Mage_Sales_Model_Order::STATE_CANCELED){
			$payment = $order->getPayment();
			$partner = $payment->getData('marketplaces_partner_code');
			if( $partner == '2xmoinscher'){
				$template = Mage::getStoreConfig ( 'tatvamarketplaces_2xmoinscher/orders/email_send_template_order_canceled' );
				$sender = Mage::getStoreConfig ( 'tatvamarketplaces_2xmoinscher/orders/sender_mail');
				
				$receiverName = Mage::getStoreConfig ( 'tatvamarketplaces_2xmoinscher/orders/receiver_name_order_canceled');
				$receiverEmail = Mage::getStoreConfig ( 'tatvamarketplaces_2xmoinscher/orders/receiver_email_order_canceled');
				
				Mage::getModel ( 'core/email_template' )
					->setDesignConfig ( array ('area' => 'adminhtml' ) )
					->sendTransactional ( 
						$template, 
						$sender, 
						$receiverEmail, 
						$receiverName, 
							array (
									'partner_order' => $payment->getData('marketplaces_partner_order'),
									'partner_customer_name' => $order->getShippingAddress()->getName(),
									'partner_order_date' => $payment->getData('marketplaces_partner_date'),
							) );
			}
		}
	
		return $this;
	}
}

?>