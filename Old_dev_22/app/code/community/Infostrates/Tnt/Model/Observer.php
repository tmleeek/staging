<?php
/**
 * created : 30 sept 2009
 * Description of the file
 * 
 * updated by <user> : <date>
 * Description of the update
 * 
 * @category SQLI
 * @package Sqli_Checkout
 * @author emchaabelasri
 * @copyright SQLI - 2009 - http://www.sqli.com
 * 
 */

/**
 * Description of the class
 * @package Sqli_Checkout
 */
class Infostrates_Tnt_Model_Observer{
	
	public function saveCustomerNotes( $observer ){

		$order = $observer->getEvent()->getOrder();
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$shippingAdress = $quote->getShippingAddress();
        //echo "<pre>"; print_r($shippingAdress);exit;
		if( $comment = $shippingAdress->getCustomerNotes() ){
		    $notes = unserialize ( $comment );
                    if(!empty($notes)){
			foreach(  $notes as $note   ){
                        $notify = $quote->getCustomerNoteNotify() ? $quote->getCustomerNoteNotify() : false;
    			//add customer notes
    			$status = $order->getStatus();
    			$order->addStatusToHistory($status, $note, $notify);
			}
                    }
		}
		return $this;	
	}
	
	
}