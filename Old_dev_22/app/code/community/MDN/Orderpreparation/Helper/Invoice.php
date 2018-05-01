<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Orderpreparation_Helper_Invoice extends Mage_Core_Helper_Abstract {

    /**
     * Store invoice id in ordertoprepare model
     *
     * @param unknown_type $OrderId
     * @param unknown_type $InvoiceId
     */
    public function StoreInvoiceId($OrderId, $InvoiceId) {
		$item = mage::helper('Orderpreparation')->getOrderToPrepareForCurrentContext($OrderId);
        $item->setinvoice_id($InvoiceId)->save();
    }

    /*
     * Check if invoice is created for 1 order
     *
     */

    public function InvoiceCreatedForOrder($OrderId) {
		$item = mage::helper('Orderpreparation')->getOrderToPrepareForCurrentContext($OrderId);
        if (($item->getinvoice_id() == null) || ($item->getinvoice_id() == ''))
            return false;
        else
            return true;
    }

    /**
     * Create invoice for order
     *
     * @param unknown_type $order
     */
    public function CreateInvoice(&$order) {
        $debug = 'Create invoice for order #'.$order->getIncrementId();

        try {
            
            if (!$order->canInvoice()) {
                $debug .= ' : Can not invoice !';
                mage::log($debug, null, 'erp_create_invoice.log');
                return false;
            }

            //Get data
            $order_to_prepare = mage::helper('Orderpreparation')->getOrderToPrepareForCurrentContext($order->getId());

            Mage::dispatchEvent('orderpreparartion_before_create_invoice', array('order' => $order));

            //create an array with items to invoice
            $shippedItems = array();
            if (!Mage::getStoreConfig('orderpreparation/create_shipment_and_invoices_options/invoice_only_shipped_items'))
            {
                foreach ($order->getAllItems() as $item) {
                    $shippedItems[$item->getId()] = $item->getqty_ordered();
                    $debug .= ', ordeitemid = '.$item->getId().' qty = '.$item->getqty_ordered();
                }
            }
            else
            {
                //create an array with shipped items
                $collection = Mage::getModel('Orderpreparation/ordertoprepare')->GetItemsToShip($order->getId());
                foreach($collection as $item)
                {
                    $shippedItems[$item->getorder_item_id()] = $item->getqty();
                    $debug .= ', ordeitemid = '.$item->getId().' qty = '.$item->getqty();
                }
                
                //add other items with 0 qty
                foreach ($order->getAllItems() as $item) {
                    if (!isset($shippedItems[$item->getId()]))
                        $shippedItems[$item->getId()] = 0;
                }
            }

            //create invoice
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($shippedItems);
			
			
            if ($invoice->canCapture())
            {
                $captureMode = '';
                //if ($invoice->getOrder()->getPayment()->getMethodInstance()->isGateway())
                //    $captureMode = 'offline';
                //else
                //{
                    $captureMode = 'online';
                //}
                
                $debug .= ', capture invoice '.$captureMode;
                $invoice->setRequestedCaptureCase($captureMode);
            }
            else
                $debug .= ',do not capture invoice';
            
			//get payment using cic method - added by nisha -start
			            
			            $payment_method = $invoice->getOrder()->getPayment()->getMethod();
					   $var_generate_invoice = 0;	
			           if($payment_method == 'cybermut_payment'){			            
                        
			           $cybermu_payment=Mage::getModel('cybermut/payment');

			            $description = $cybermu_payment->getConfigData('description')
			            ? $cybermu_payment->getConfigData('description')
			            : Mage::helper('cybermut')->__('Invoice %s', $invoice->getOrder()->getRealOrderId());
						
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
						 
						 if(in_array('cdr=1',$var_res) && in_array('lib=paiement accepte',$var_res))
		                   {
		                     $var_generate_invoice = 1;
		    			   }		             
			             
			            }
						else
						{
						   $var_generate_invoice = 1;	
						}
			//get payment using cic method - added by nisha -end
            //save invoice
			
			if($var_generate_invoice == 1)
			{
	            $invoice->register();
	            $invoice->getOrder()->setIsInProcess(true);
	            $transactionSave = Mage::getModel('core/resource_transaction')
	                            ->addObject($invoice)
	                            ->addObject($invoice->getOrder())
	                            ->save();
	            //$invoice->save();

	            //link order & invoice
	            $this->StoreInvoiceId($order->getid(), $invoice->getincrement_id());
	            $debug .= ', invoiceid = '.$invoice->getincrement_id();

	            //validate payment
	            //$payment = $order->getPayment();
	            //$payment->pay($invoice);
	            //$payment->save();


	            //$order->save();

	            Mage::dispatchEvent('orderpreparartion_after_create_invoice', array('order' => $order, 'invoice' => $invoice));
			}
			else
			{
				$order = Mage::getModel('sales/order')->load($invoice->getOrderId());
          		//$order->setState(Sqli_Sales_Model_Order::STATE_CICPAYMENTSTATUS_HOLD, true)->save();
          		$order->hold();
                //$order->save();
                $order->setState(Asperience_Deleteallorders_Model_Order::STATE_CICPAYMENTSTATUS_HOLD, true)->save();
       	  		$invoice->sendblockordercicEmail();
                if($payment_method == 'cybermut_payment')
                  {
                    Mage::getSingleton('core/session')->addError($this->__('The payment has been rejected by the bank and the customer has been notified.'));
                  }
                  else
                  {

                    Mage::getSingleton('core/session')->addError($this->__('Can not save invoice'));
                  }

			
			     $debug .= ',Payment is not accepted';
                 mage::log($debug, null, 'erp_create_invoice.log');
                throw new Exception('Error while creating Invoice for Order ' . $order->getincrement_id() . ': Payment is not accepted');
			}

        } catch (Exception $ex) {
            $debug .= ', '.$ex->getMessage();
            mage::log($debug, null, 'erp_create_invoice.log');
            throw new Exception('Error while creating Invoice for Order ' . $order->getincrement_id() . ': ' . $ex->getMessage());
        }
        
        mage::log($debug, null, 'erp_create_invoice.log');
        return true;
    }

}
