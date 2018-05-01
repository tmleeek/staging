<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Cybermutforeign
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cybermutforeign Payment Front Controller
 *
 * @category   Mage
 * @package    Mage_Cybermutforeign
 * @name       Mage_Cybermutforeign_PaymentController
 * @author	   Magento Core Team <core@magentocommerce.com>, Quadra Informatique - Nicolas Fischer <nicolas.fischer@quadra-informatique.fr>
 */
class Mage_Cybermutforeign_PaymentController extends Mage_Core_Controller_Front_Action
{
    /**
     * Order instance
     */
    protected $_order;
	protected $_quote;
	protected $_cybermutforeignResponse = null;

    /**
     *  Get order
     *
     *  @param    none
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
            $session = Mage::getSingleton('checkout/session');
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByIncrementId($session->getLastRealOrderId());
        }
        return $this->_order;
    }

    /**
     * When a customer chooses Cybermutforeign on Checkout/Payment page
     *
     */
	 
	public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setCybermutforeignPaymentQuoteId($session->getLastQuoteId());

        if ($this->getQuote()->getIsMultiShipping()) {
            $realOrderIds = explode(',', $session->getRealOrderIds());
            $session->setCybermutforeignRealOrderIds($session->getRealOrderIds());
        } else {
            $realOrderIds = array($session->getLastRealOrderId());
            $session->setCybermutforeignRealOrderIds($session->getLastRealOrderId());
        }

        foreach ($realOrderIds as $realOrderId) {
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($realOrderId);

            if (!$order->getId()) {
                $this->norouteAction();
                return;
            }

            $order->addStatusToHistory(
                $order->getStatus(), Mage::helper('cybermutforeign')->__('Customer was redirected to Cybermutforeign')
            );
            $order->save();
        }

        $this->getResponse()
             ->setBody($this->getLayout()
                ->createBlock('cybermutforeign/redirect')
                ->setOrder($order)
                ->toHtml());

        $session->unsQuoteId();
    }
	
	public function getQuote()
    {
        if (!$this->_quote) {
            $session = Mage::getSingleton('checkout/session');
            $this->_quote = Mage::getModel('sales/quote')->load($session->getCybermutforeignPaymentQuoteId());

            if (!$this->_quote->getId()) {
                $realOrderIds = $this->getRealOrderIds();
                if (count($realOrderIds)) {
                    $order = Mage::getModel('sales/order')->loadByIncrementId($realOrderIds[0]);
                    $this->_quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
                }
            }
        }
        return $this->_quote;
    }
	
	public function getRealOrderIds()
    {
        if (!$this->_realOrderIds) {
            if ($this->_cybermutforeignResponse) {
                $this->_realOrderIds = explode(',', $this->_cybermutforeignResponse['reference']);
            } elseif($realOrderIds = Mage::getSingleton('checkout/session')->getCybermutforeignRealOrderIds()) {
                $this->_realOrderIds = explode(',', $realOrderIds);
            } else {
                return array();
            }
        }
        return $this->_realOrderIds;
    }
	 
   /*	public function redirectAction()
	{
		$session = Mage::getSingleton('checkout/session');

		$session->setCybermutforeignPaymentQuoteId($session->getQuoteId());
        

       	$orderIds = Mage::getSingleton('core/session')->getOrderIds();
	    	foreach ($orderIds as $orderId) {
	    		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
                
	    		$order->addStatusToHistory(
			      $order->getStatus(),
		         	Mage::helper('cybermutforeign')->__('Customer was redirected to Cybermutforeign')
		         );
	    		$order->save();
	    	}
echo '<pre>';print_r($order->getData());exit;
		$this->getResponse()
			->setBody($this->getLayout()
				->createBlock('cybermutforeign/redirect')
				->setOrder($order)
				->toHtml());

        $session->unsQuoteId();
    }*/

	/**
	 *  Cybermutforeign response router
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function notifyAction()
	{ 
		$model = Mage::getModel('cybermutforeign/payment');
        
        if ($this->getRequest()->isPost()) {
			$postData = $this->getRequest()->getPost();
        	$method = 'post';

		} else if ($this->getRequest()->isGet()) {
			$postData = $this->getRequest()->getQuery();
			$method = 'get';

		} else {  
			$model->generateErrorResponse();
		}

		if ($model->getConfigData('debug_flag')) {
			Mage::getModel('cybermutforeign/api_debug')
				->setResponseBody(print_r($postData ,1))
				->save();
		}

		$returnedMAC = $postData['MAC'];
		$correctMAC = $model->getResponseMAC($postData);

		$order = Mage::getModel('sales/order')
			->loadByIncrementId($postData['reference']);

		if (!$order->getId()) {
			$model->generateErrorResponse();
		}

		if ($returnedMAC == $correctMAC) {
			if ($model->isSuccessfulPayment($postData['code-retour'])) {



			    // Déblocage de la commande si nécessaire
			    if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
    				$order->unhold();
			    }

			    if (!$status = $model->getConfigData('order_status_payment_accepted')) {
                    $status = $order->getStatus();
                }



			    /*$order->addStatusToHistory(
					$status,
					$model->getSuccessfulPaymentMessage($postData),
					true
				);

				if ($model->getConfigData('order_status_payment_accepted') == 'processing') {
					$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, false);
				}*/
                
                $message = $model->getSuccessfulPaymentMessage($postData);
				
			    if ($status == Mage_Sales_Model_Order::STATE_PROCESSING) {
                    $order->setState(
                        Mage_Sales_Model_Order::STATE_PROCESSING,
                        $status,
                        $message
                    );
                } else if ($status == Mage_Sales_Model_Order::STATE_COMPLETE) {
                    $order->setState(
                        Mage_Sales_Model_Order::STATE_COMPLETE,
                        $status,
                        $message
                    );
                } else {
                    $order->addStatusToHistory(
    					$status,
    					$message,
    					true
    				);
				}

				//$order->sendNewOrderEmail();
				//$order->setEmailSent(true);

				//$this->saveInvoice($order);
				
			 } else {
			 	/*$order->addStatusToHistory(
					$model->getConfigData('order_status_payment_refused'),
					$model->getRefusedPaymentMessage($postData)
				);

				if ($model->getConfigData('order_status_payment_refused') == Mage_Sales_Model_Order::STATE_CANCELED) {
					$order->cancel();
				}*/

				//add by nisha
				if ($model->getConfigData('order_status_payment_refused') == Sqli_Sales_Model_Order::STATE_CICPAYMENTSTATUS_PAYMENTREFUSED) {
					$order->cancel();
				}

                $messageError = $model->getRefusedPaymentMessage($postData);

		        if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
					$order->unhold();
				}
		        
		        if (!$status = $model->getConfigData('order_status_payment_refused')) {
                    $status = $order->getStatus();
                }
		        
                $order->addStatusToHistory(
					$status,
					$messageError
				);

				if ($status == Mage_Sales_Model_Order::STATE_HOLDED && $order->canHold()) {
					$order->hold();
				}
			 }
			
			$order->save();
			if ($method == 'post') {
				$model->generateSuccessResponse();
			} else if ($method == 'get') {
				return;
			}

        } else {
            $order->addStatusToHistory(
                $order->getStatus(),
                Mage::helper('cybermutforeign')->__('Returned MAC is invalid. Order cancelled.')
            );
            $order->cancel();
            $order->save();
            $model->generateErrorResponse();
        }
    }

    /**
     *  Save invoice for order
     *
     *  @param    Mage_Sales_Model_Order $order
     *  @return	  boolean Can save invoice or not
     */
    protected function saveInvoice(Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice()) {
            
            $version = Mage::getVersion();
            $version = substr($version, 0, 5);
            $version = str_replace('.', '', $version);
            while (strlen($version) < 3) {
            	$version .= "0";
            }

            if (((int)$version) < 111) {
	            $convertor = Mage::getModel('sales/convert_order');
	            $invoice = $convertor->toInvoice($order);
	            foreach ($order->getAllItems() as $orderItem) {
	               if (!$orderItem->getQtyToInvoice()) {
	                   continue;
	               }
	               $item = $convertor->itemToInvoiceItem($orderItem);
	               $item->setQty($orderItem->getQtyToInvoice());
	               $invoice->addItem($item);
	            }
	            $invoice->collectTotals();

            } else {
            	$invoice = $order->prepareInvoice();
			}

			$invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
               ->addObject($invoice)
               ->addObject($invoice->getOrder())
               ->save();
            return true;
        }

        return false;
    }

	/**
	 *  Success payment page
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function successAction()
	{
		$session = Mage::getSingleton('checkout/session');
		$session->setQuoteId($session->getCybermutforeignPaymentQuoteId());
		$session->unsCybermutforeignPaymentQuoteId();
		
	   /*	$order = $this->getOrder();
		
		if (!$order->getId()) {
			$this->norouteAction();
			return;
		}

		$order->addStatusToHistory(
			$order->getStatus(),
			Mage::helper('cybermutforeign')->__('Customer successfully returned from Cybermutforeign')
		);
        
		$order->save();*/

           $orderIds = Mage::getSingleton('core/session')->getOrderIds();
	    	foreach ($orderIds as $orderId) {
	    		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
                
	    		$order->addStatusToHistory(
			      $order->getStatus(),
		         	Mage::helper('cybermutforeign')->__('Customer successfully returned from Cybermutforeign')
		         );
	    		$order->save();
	    	}
        
		$this->_redirect('checkout/multishipping/success');
	}

	/**
	 *  Failure payment page
	 *
	 *  @param    none
	 *  @return	  void
	 */
	public function errorAction()
	{
        $session = Mage::getSingleton('checkout/session');
        $model = Mage::getModel('cybermutforeign/payment');

        //$order = $this->getOrder();
     $orderIds = Mage::getSingleton('core/session')->getOrderIds();
     foreach ($orderIds as $orderId) {
	    		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {

            if($order->getStatus() == 'cicpaymentstatus')
            {
              	Mage::getSingleton('checkout/session')->addPaymentrefused(Mage::helper('tatvasales')->__("Your payment has been rejected by our bank due to incorrect data or to the refusal of your financial institution to honor the transaction. We apologise for the inconvenience and we invite you to either renew your order making sure that the card data are correct or to use another payment method."));
                $this->_redirect('checkout/multishipping/billing');
                return;  
            }
			
      	    if ($model->getConfigData('order_status_payment_canceled') == 'cicpaymentcustomercancelstatus') 			 {
              $order->cancel();
			}

           
            if (!$status = $model->getConfigData('order_status_payment_canceled')) {
                $status = $order->getStatus();
				
            }
           
            $order->addStatusToHistory(
    			$status,
    			$this->__('Order was canceled by customer')
    		);
   
    		if ($status == Mage_Sales_Model_Order::STATE_HOLDED && $order->canHold()) {
    			$order->hold();
    		} else if ($status == Mage_Sales_Model_Order::STATE_CANCELED) {
				$order->cancel();
			}
            
            $order->save();
           }
             
        }


	Mage::getSingleton('checkout/session')->addCustomerordercancel(Mage::helper('tatvasales')->__("You did not complete your payment and your order has been canceled. If you have encountered a problem during your payment, we invite you to either change your payment method or to contact us by phone at +33 (0) 811 69 69 29 (French cost call) or by e-mail at <a href='mailto:contact@az-boutique.fr'>contact@az-boutique.fr</a>."));



        
        //Mage::getSingleton('checkout/session')->addError(Mage::helper('sqlisales')->__("Order cancelled"));
        //$this->_redirect('checkout/cart');
       $this->_redirect('checkout/multishipping/billing');
     }
}
