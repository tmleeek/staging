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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order edit controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Adminhtml/controllers/Sales/Order/InvoiceController.php';
class Tatva_Adminhtml_Sales_Order_InvoiceController extends Mage_Adminhtml_Sales_Order_InvoiceController
{
    

    /**
     * Save invoice
     * We can save only new invoice. Existing invoices are not editable
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost('invoice');
        $orderId = $this->getRequest()->getParam('order_id');

        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            $invoice = $this->_initInvoice();
            if ($invoice) {	
            	
			            $var_generate_invoice = 0;
			            $payment_method = $invoice->getOrder()->getPayment()->getMethod();

			           if($payment_method == 'cybermut_payment'){

			            if($data['capture_case'] == Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE){

			                $cybermu_payment=Mage::getModel('cybermut/payment');

			            $description = $cybermu_payment->getConfigData('description')
			            ? $cybermu_payment->getConfigData('description')
			            : Mage::helper('cybermut')->__('Invoice %s', $invoice->getOrder()->getRealOrderId());
						
						
						
						//$description='ExempleTexteLibre  cybermut_payment';
			                     $fields = array(
			                        'version'        => $cybermu_payment->getConfigData('version'),
			                        'TPE'            => $cybermu_payment->getConfigData('tpe_no'),
			                        'date'           => date('d/m/Y:H:i:s'),
			                        'date_commande'  => date("d/m/Y", strtotime($invoice->getOrder()->getCreatedAt())),
			                        'montant'        => round($invoice->getOrder()->getGrandTotal(),2).$invoice->getOrder()->getBaseCurrencyCode(),
			                        'montant_a_capturer' => round($invoice->getOrder()->getGrandTotal(),2).$invoice->getOrder()->getBaseCurrencyCode(),
			                        'montant_deja_capture' => '0.0000'.$invoice->getOrder()->getBaseCurrencyCode(),
			                        'montant_restant' => '0.0000'.$invoice->getOrder()->getBaseCurrencyCode(),
			                        'reference'      => $invoice->getOrder()->getRealOrderId(),
			                        'texte-libre'    => $description,
			                        'lgue'           => $cybermu_payment->_getLanguageCode(),
			                        'societe'        => $cybermu_payment->getConfigData('site_code')
			                        );
								  
			                     $mac = $cybermu_payment->_getMACInvoice($fields); 
			                    $date = date('d/m/Y:H:i:s');
			                    $date = str_replace("/","%2F",$date);
			                    $date = str_replace(":","%3A",$date);

			                    $date_commande = date("d/m/Y", strtotime($invoice->getOrder()->getCreatedAt()));
			                    $date_commande = str_replace("/","%2F",$date_commande);
								
							   
								
			  		    $data_1 = "version=".$cybermu_payment->getConfigData('version')."&TPE=".$cybermu_payment->getConfigData('tpe_no')."&date=".$date."&date_commande=".$date_commande."&montant=".round($invoice->getOrder()->getGrandTotal(),2).$invoice->getOrder()->getBaseCurrencyCode()."&montant_a_capturer=".round($invoice->getOrder()->getGrandTotal(),2).$invoice->getOrder()->getBaseCurrencyCode()."&montant_deja_capture=0.0000".$invoice->getOrder()->getBaseCurrencyCode()."&montant_restant=0.0000".$invoice->getOrder()->getBaseCurrencyCode()."&reference=".$invoice->getOrder()->getRealOrderId()."&texte-libre=".$description."&lgue=".$cybermu_payment->_getLanguageCode()."&societe=".$cybermu_payment->getConfigData('site_code')."&MAC=".$mac;

			            //print_r($invoice->getOrder()) paiement accepte;
                        $url = $cybermu_payment->getConfigData('test_mode') ? 'https://ssl.paiement.cic-banques.fr/test/capture_paiement.cgi' : 'https://ssl.paiement.cic-banques.fr/capture_paiement.cgi';

			        	 $ch=curl_init();
			             curl_setopt($ch, CURLOPT_URL, $url);
			             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			             curl_setopt($ch, CURLOPT_POST, 1) ;
			             curl_setopt($ch, CURLOPT_POSTFIELDS, $data_1);
			             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			             curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			             $results = curl_exec($ch);
			             curl_close($ch);

			             $var_res = explode(chr(10),$results);
						 
			             }
			             elseif($data['capture_case'] == Mage_Sales_Model_Order_Invoice::NOT_CAPTURE || $data['capture_case'] == Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE)
			             {
			                $var_generate_invoice = 1;
			             }
			            }



						if($payment_method == 'cybermut_payment')
						{
			                if($data['capture_case'] == Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE)
			                {
			                   if(in_array('cdr=1',$var_res) && in_array('lib=paiement accepte',$var_res))
			                   {
			                     $var_generate_invoice = 1;
			    			   }
			                }

			            }
			            else
			            {
							$var_generate_invoice = 1;
			                    //echo '<pre>';print_r($invoice->getOrder()->getData());
			                   //$order = Mage::getModel('sales/order')->load($orderId);
			            	//$order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, true)->save();
			            }
			  
			if($var_generate_invoice == 1)
            {	 	
		    

                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase($data['capture_case']);
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                }

                $invoice->register();

                if (!empty($data['send_email'])) {
                    $invoice->setEmailSent(true);
                }

                $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $shipment = false;
                if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
                    $shipment = $this->_prepareShipment($invoice);
                    if ($shipment) {
                        $shipment->setEmailSent($invoice->getEmailSent());
                        $transactionSave->addObject($shipment);
                    }
                }
                $transactionSave->save();
				
				if($payment_method == 'cybermut_payment')
                  {
		                  if($data['capture_case'] == Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE)
		                  {   $order = Mage::getModel('sales/order')->load($invoice->getOrderId());
		                      $auth_num = '';
		                      if(!empty($var_res[4]))
		                      {
		                        $auth_num = explode("=",$var_res[4]);

		                        //comment by nisha $order->setOrderCicauth($auth_num[1]);
		                        //$order->setOrderCicauth(getAttributeByValue('order_cicauth', 'blue'));
		                      }
		                      $order->addStatusToHistory(
		                			$order->getStatus(),
		                			$this->__('The card was debited with the Amount %s.',number_format($invoice->getOrder()->getGrandTotal(),2))
		                		);
		                      $order->save();
		                      $this->_getSession()->addSuccess($this->__('The payment has been approved by the bank.'));
		                      $this->_getSession()->addSuccess($this->__('The card was debited with the Amount %s.',number_format($invoice->getOrder()->getGrandTotal(),2)));
		                  }
						  else
						  {
							  	if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
				                    $this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
				                } elseif (!empty($data['do_shipment'])) {
				                    $this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
				                } else {
				                	
								   
				                    $this->_getSession()->addSuccess($this->__('The invoice has been created.'));
				                }  	
						  }
						  
				 } 
				  else
				  {
				           if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
			                    $this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
			                } elseif (!empty($data['do_shipment'])) {
			                    $this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
			                } else {
			                	
							   
			                    $this->_getSession()->addSuccess($this->__('The invoice has been created.'));
			                }  				
				  	
				  }

                

                // send invoice/shipment emails
                $comment = '';
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
                try {
                    $invoice->sendEmail(!empty($data['send_email']), $comment);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $this->_getSession()->addError($this->__('Unable to send the invoice email.'));
                }
                if ($shipment) {
                    try {
                        $shipment->sendEmail(!empty($data['send_email']));
                    } catch (Exception $e) {
                        Mage::logException($e);
                        $this->_getSession()->addError($this->__('Unable to send the shipment email.'));
                    }
                }
                Mage::getSingleton('adminhtml/session')->getCommentText(true);
                $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
			  }
			  else
			  {
			    //cic payment status - hold code
				
				$order = Mage::getModel('sales/order')->load($invoice->getOrderId());
          		//$order->setState(Sqli_Sales_Model_Order::STATE_CICPAYMENTSTATUS_HOLD, true)->save();
          		$order->hold();
                //$order->save();
                $order->setState(Asperience_Deleteallorders_Model_Order::STATE_CICPAYMENTSTATUS_HOLD, true)->save();
       	  		$invoice->sendblockordercicEmail();
                if($payment_method == 'cybermut_payment')
                  {
                    $this->_getSession()->addError($this->__('The payment has been rejected by the bank and the customer has been notified.'));
                  }
                  else
                  {

                    $this->_getSession()->addError($this->__('Can not save invoice'));
                  }

		 		$this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
				
					
			  
			  }	
            } else {
                $this->_redirect('*/*/new', array('order_id' => $orderId));
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Unable to save the invoice.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/new', array('order_id' => $orderId));
    }
}
