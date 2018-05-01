<?php

require_once ('Mage/Checkout/controllers/OnepageController.php');
class Tatva_Checkout_OnepageController extends Mage_Checkout_OnepageController
{   
    /**
     * Order success action
     */
   
    public function successAction()
    {
        $session = $this->getOnepage()->getCheckout();
        if (!$session->getLastSuccessQuoteId()) {
            $this->_redirect('checkout/cart');
            return;
        }

        $lastQuoteId = $session->getLastQuoteId();
        $lastOrderId = $session->getLastOrderId();
        $lastRecurringProfiles = $session->getLastRecurringProfileIds();
        if (!$lastQuoteId || (!$lastOrderId && empty($lastRecurringProfiles))) {
            $this->_redirect('checkout/cart');
            return;
        }

        $order =  Mage::getModel('sales/order')->load($lastOrderId);

        if($order->getShippingMethod() == 'colissimocityssimo_colissimocityssimo' || $order->getShippingMethod() == 'colissimolocalstore_colissimolocalstore' || $order->getShippingMethod() == 'colissimopostoffice_colissimopostoffice' )
        {
            $order->setRelayName(Mage::getSingleton('core/session')->getData('set_relay_name'));
            $order->setRelayAddress(Mage::getSingleton('core/session')->getData('set_relay_address'));
            $order->setRelayAddress2(Mage::getSingleton('core/session')->getData('set_relay_address2'));
            $order->setRelayAddress3(Mage::getSingleton('core/session')->getData('set_relay_address3'));
            $order->setRelayCity(Mage::getSingleton('core/session')->getData('set_relay_city'));
            $order->setRelayPostalcode(Mage::getSingleton('core/session')->getData('set_relay_postalcode'));
            $order->setRelayId(Mage::getSingleton('core/session')->getData('set_relay_id'));
            $order->setRelayCode(Mage::getSingleton('core/session')->getData('set_relay_code'));
            $order->save();
        }
        else if($order->getShippingMethod() == 'colissimoappointment_colissimoappointment')
        {
            $order->setRelayCode('RDV');
            $order->save();
        }
        else if($order->getShippingMethod() == 'colissimo_colissimo')
        {
            if($order->getsubtotal() >= 35)
            {
                $order->setRelayCode('DOS');
            }
            else
            {
                $order->setRelayCode('DOM');
            }
            $order->save();
        }

        $session->clear();
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        Mage::dispatchEvent('checkout_onepage_controller_success_action', array('order_ids' => array($lastOrderId)));
        $this->renderLayout();
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction()
    {
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                        array('request'=>$this->getRequest(),
                            'quote'=>$this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['goto_section'] = 'payment';
                $result['update_section'] = array(
                    'name' => 'payment-method',
                    'html' => $this->_getPaymentMethodsHtml()
                );
            }
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function tatvapopupAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function tatvachoicepopupAction()
    {    
    	 //echo "hellopopup"; exit;
        $this->loadLayout()->renderLayout();
    }

    public function tatvamobilepopupAction()
    {
        $this->getOnepage()->getQuote()->getShippingAddress()->setMobilephone($this->getRequest()->getParam('socolissimo_mobile_phone'));
        $this->saveShippingMethodAction();
    }

    public function confirmrelayAction()
    {    
    	
        $relay_type = $this->getRequest()->getParam('relay_type');
        $relay_name = $this->getRequest()->getParam('relay_name');
        $relay_address = $this->getRequest()->getParam('relay_address');
        $relay_address2 = $this->getRequest()->getParam('relay_address2');
        $relay_address3 = $this->getRequest()->getParam('relay_address3');
        $relay_city = $this->getRequest()->getParam('relay_city');
        $relay_postalcode = $this->getRequest()->getParam('relay_postalcode');
        $relay_id = $this->getRequest()->getParam('relay_id');
        $relay_code = $this->getRequest()->getParam('relay_code');
        $shipping_address_id = $this->getRequest()->getParam('shipping_address_id');
        $selected_ship_method = $this->getRequest()->getParam('selected_ship_method');
           
        Mage::getSingleton('core/session')->setData('set_relay_type',$relay_type);
        Mage::getSingleton('core/session')->setData('set_relay_name',$relay_name);
        Mage::getSingleton('core/session')->setData('set_relay_address',$relay_address);
        Mage::getSingleton('core/session')->setData('set_relay_address2',$relay_address2);
        Mage::getSingleton('core/session')->setData('set_relay_address3',$relay_address3);
        Mage::getSingleton('core/session')->setData('set_relay_city',$relay_city);
        Mage::getSingleton('core/session')->setData('set_relay_postalcode',$relay_postalcode);
        Mage::getSingleton('core/session')->setData('set_relay_id',$relay_id);
        Mage::getSingleton('core/session')->setData('set_relay_code',$relay_code);
        Mage::getSingleton('core/session')->setData('set_shipping_address_id',$shipping_address_id);
        Mage::getSingleton('core/session')->setData('set_selected_ship_method',$selected_ship_method);
		
		
		           $current = Mage::getSingleton ( 'checkout/session' )->getQuote ();
				   Mage::register ( 'current_quote', $current );
				   $address = $current->getShippingAddress ();

				   ( string ) $postcode = $relay_postalcode;
				   if (substr ( $postcode, 0, 2 ) == 20) {
				    $regioncode = substr ( $postcode, 0, 3 );
				    switch ($regioncode) {
				     case 202 :
				      $regioncode = '2B';
				      break;
				     default:
				      $regioncode = '2A';
				      break;
				    }
				   } else {
				    $regioncode = substr ( $postcode, 0, 2 );
				   }

				   Mage::app ()->getLocale ()->setLocaleCode ( 'en_US' );
				   $region = Mage::getModel ( 'directory/region' )->loadByCode ( $regioncode, $address->getCountryId () );
				   $regionname = $region->getDefaultName ();
				   $regionid = $region->getRegionId ();
				   $address->setRegion ( $regionname );
				   $address->setRegionId ( $regionid );
				   $address->setPostcode ( $postcode );
				   $address->setStreet ( urldecode ($relay_address.$relay_address2.$relay_address3) );
				   $address->setCity ( urldecode ( $relay_city ) );				   
				   $address->save ();
				   $current->setShippingAddress ( $address );
				   $current->save ();

		
		
		

        $shippingMethods = array($shipping_address_id => $selected_ship_method);

	    try
        {
            $shippingAdress = $this->getOnepage()->getQuote()->getShippingAddress();

            $this->getOnepage()->getQuote()->getShippingAddress()->setShippingMethods($shippingMethods);

            $this->saveShippingMethodAction();
        }
        catch (Exception $e){
            Mage::getSingleton('checkout/session')->addError($e->getMessage());
            $this->_redirect('*/onepage');
        }
    }     
}
