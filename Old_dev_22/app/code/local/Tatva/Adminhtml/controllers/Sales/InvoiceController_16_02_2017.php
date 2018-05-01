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
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Adminhtml/controllers/Sales/InvoiceController.php';
class Tatva_Adminhtml_Sales_InvoiceController extends Mage_Adminhtml_Sales_InvoiceController
{
    public function exportaccountAction(){
       
      $invoicesIds = $this->getRequest()->getPost('invoice_ids');
      $path = Mage::getBaseDir('var') . DS . 'export' . DS;
      if(file_exists(Mage::getBaseDir('var') . DS . 'export' . DS. 'exportaccounting.csv'))
      {
        unlink(Mage::getBaseDir('var') . DS . 'export' . DS. 'exportaccounting.csv');
      }
	  
      $file = 'exportaccounting.csv';
      $fp = @fopen($path.$file, 'w');
     
      for($i=0;$i<count($invoicesIds);$i++)
      {
                  $invoice = Mage::getModel("sales/order_invoice")->load($invoicesIds[$i]);

                   /*echo '<pre>';
                   print_r($this->order->getData());  exit;*/
                  $order_id = $invoice->getOrderId();

                  $order = Mage::getModel('sales/order')->load($order_id);
/*echo '<pre>';
                   print_r($order->getFullTaxInfo());  exit;*/
                  
                  $tvaDetail = array ();
            		foreach ( $invoice->getAllItems () as $item ) {
            			$orderItem = $item->getOrderItem ();
            			$codeTva = 't' . ($orderItem->getTaxPercent () * 1);
            			$tvaDetail [$codeTva] = array (
            					'sstotht' => 0,
            					'rem' => 0,
            					'port' => 0,
            					'pTva' => 0,
            					'tva' => 0,
            					'totht' => 0 );

            		}
            		if ($invoice->getShippingAmount () > 0) {
            			$codeTva = 't' . ($order->getPercentTaxShipping () * 1);
            			$tvaDetail [$codeTva] = array (
            					'sstotht' => 0,
            					'rem' => 0,
            					'port' => 0,
            					'pTva' => 0,
            					'tva' => 0,
            					'totht' => 0 );

            		}
                        //echo "<pre>";print_r($order->getData());die();
                        $total = array();
                         $from = $order->getOrderCurrencyCode();
                         $divided_amt = $order->getStoreToOrderRate();
                         if($from == "USD")
                         {
                             $divided_amt = $order->getStoreToBaseRate();
                         }
            		foreach ( $invoice->getAllItems () as $item ) {
                            $_product = Mage::getModel('catalog/product')->load($item->getProductId());
			    $weee_amt = Mage::helper('weee')->getAmount($_product);
            			$orderItem = $item->getOrderItem ();
            			 $codeTva = 't' . ($orderItem->getTaxPercent () * 1);
            			$tvaDetail [$codeTva] ['pTva'] = $orderItem->getTaxPercent ();
                                if($from=="GBP")
                                {
                                    $tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] + $item->getBaseRowTotal ();
                                }
                                elseif($from=="USD")
                                {
                                    $tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] + $item->getBaseRowTotal ()*$divided_amt;
                                }
                                else
                                {
                                    $tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] + $item->getRowTotal ();
                                }
            			$tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $item->getBaseTaxAmount ();
            			$tvaDetail [$codeTva] ['totht'] = $tvaDetail [$codeTva] ['totht'] + $item->getRowTotal ()  - $orderItem->getDiscountAmount ();
            			$tvaDetail [$codeTva] ['rem'] = $tvaDetail [$codeTva] ['rem'] + $item->getDiscountAmount ();
                                
                                    if(isset($weee_amt) && $weee_amt!=0)
                                {
                                $tvaDetail [$codeTva] ['dee'] = $weee_amt;
                                }
                                //array_push($total, $tvaDetail [$codeTva] ['sstotht'],$tvaDetail [$codeTva] ['tva']);

            		}
                        
                        
                        // if you want it rounded:
                        //echo "<pre>";print_r($order->getData());die();
                        
                        //echo $divided_amt;die();
            		if ($invoice->getShippingAmount () > 0) {
            		  $orderItem = $item->getOrderItem ();
            			$codeTva = 't' . ($orderItem->getTaxPercent () * 1);
            			$tvaDetail [$codeTva] ['pTva'] = $orderItem->getTaxPercent ();
            			if($from=="GBP")
                                {
                                   
                                    $tvaDetail [$codeTva] ['port'] = $invoice->getBaseShippingAmount();
                                }
                                elseif($from=="USD")
                                {
                                   $tvaDetail [$codeTva] ['port'] = $invoice->getShippingAmount ()*$divided_amt;
                                }
                                else
                                {
                                   $tvaDetail [$codeTva] ['port'] = $invoice->getShippingAmount ();
                                }
                                if($from=="GBP")
                                {
                                    $tvaDetail [$codeTva] ['totht'] = ($tvaDetail [$codeTva] ['totht'] + $invoice->getShippingAmount ())/$divided_amt;
                                }
                                elseif($from=="USD")
                                {
                                    $tvaDetail [$codeTva] ['totht'] = ($tvaDetail [$codeTva] ['totht'] + $invoice->getShippingAmount ())*$divided_amt;
                                }
                                else
                                {
                                    $tvaDetail [$codeTva] ['totht'] = $tvaDetail [$codeTva] ['totht'] + $invoice->getShippingAmount ();
                                }
                                
                                    $tvaDetail [$codeTva] ['dee'] = $weee_amt;
                                $tax = $invoice->getShippingAmount () * $orderItem->getTaxPercent () / 100;
                                $ship = $tvaDetail [$codeTva] ['tva'];
                                if($from=="GBP")
                                {
                                    $VAT = $invoice->getShippingAmount ()/$divided_amt;
                                }
                                elseif($from=="USD")
                                {
                                    $VAT = $invoice->getShippingAmount ()*$divided_amt;
                                }
                                else
                                {
                                    $VAT = $invoice->getShippingAmount ();
                                }
                                //echo $VAT;
            			$tvaShipping = $VAT * $orderItem->getTaxPercent () / 100;
                                
            			//echo $tvaDetail[$codeTva]['tva'];
                                /*$amt = $tvaDetail [$codeTva] ['tva'] + $tvaShipping;
                                $tvaamt = floor($amt * 100) / 100;*/
                                
                                //$tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $tvaShipping;
                                /*if($from=="GBP")
                                {
                                    $tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $tvaShipping;
                                }
                                elseif($from=="USD")
                                {
                                    $tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $tvaShipping;
                                }
                                else
                                {*/
                                    ;
                                    $tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $tvaShipping;
                               /* }*/
                                     array_push($total,$tvaDetail [$codeTva] ['sstotht'],$tvaDetail [$codeTva] ['port'],$tvaDetail [$codeTva] ['tva'],$tvaDetail [$codeTva] ['dee']);
            		}
                     $final_total = array_sum($total);
                        //echo "<pre>";print_r($tvaDetail);die();
                        //echo $order->getOrderCurrencyCode();die();
                  $code = '';
                  $code_subtotal = '';
                  $code_shipping = '';
                  $code_rebate = '';
                  $code_vat = '';

                  $payment = $order->getPayment();
                  //echo '<pre>';print_r($payment->getData());exit;



                    //echo 'hello=='.$order->getShippingAddress()->getCountry_id(); exit;
					
					if($payment->getAdditionalData())
					{
                                            //echo '<pre>';print_r($payment->getData());exit;
                      $payment_arr = unserialize($payment->getAdditionalData());
					  
					  if($payment_arr['payment_method'] != '')
					  {
	                      if($payment_arr['payment_method'] == 'Virement bancaire')
						  {
						  	$code = '9VRMT';
						  }
						  else if($payment_arr['payment_method'] == 'PayPal')
						  {
						  	$code = '9PAYPAL';
						  }
                                          }
					  if($payment_arr['component_mode'] == 'amazon')
					  {
					  	$code = '9AMAZON';
					  }
				    }
				 
                  $date = $invoice->getCreatedAt();

                  $invoiceDate = date("d/m/Y", strtotime($date));
                   //echo $payment->getMethod();exit;
                  //payment type
				  
                  if($payment->getMethod() == 'transferpayment')
                  {
                      $code = '9VRMT';
                  }
                  else if($payment->getMethod() == 'atos_standard')
                  {
                      $code = '9CB';
                  }

                  else if($payment->getMethod() == 'mandatcash')
                  {
                      $code = '9MANDAT';
                  }
                  else if($payment->getMethod() == 'checkmo')
                  {
                      $code = '9CHEQUE';
                  }
				  else if($payment->getMethod() == 'cybermut_payment')
                  {
                      $code = '9CB';
                  }
                  else if($payment->getMethod() == "cybermutforeign_payment")
                  {
                      //$code = '8DB';
                      $code = '9CB';
                  }
                  else if($payment->getMethod() == 'paypal_standard' || $payment->getMethod() == "paypal_billing_agreement" || $payment->getMethod() == "paypal_mecl")
                  {
                      $code = '9PAYPAL';
                  }
                  else if($payment->getMethod() == "m2epropayment")
                  {
                      //$code = '9VRMT';
                      $code = '9PAYPAL';
                  }
                  if($order->getMarketplacesPartnerCode() == 'rueducommerce' || $payment->getMethod() == 'rueducommerce')
                  {
                      $code = '9RDC';
                  }
                  else if($order->getMarketplacesPartnerCode() == 'pixmania' || $payment->getMethod() == 'pixmania')
                  {
                      $code = '9PIX';
                  }
                  else if($payment->getMethod == 'mandatadministratif')
                  {
                      $code = '9MA';
                  }
                  //subtotal
                  //echo number_format($order->getGrandTotal(),2);die();
                 // echo $divided_amt;die();
                  $value = '';
                  $customer_name = iconv('UTF-8','UCS-2LE',$order->getCustomerFirstname().' '.$order->getCustomerLastname());
                  
                  if($from=="GBP")
                  {
                     // echo $order->getGrandTotal();die();
                      $amt1 = $order->getGrandTotal()/$divided_amt;
                      //$amt1 = $final_total;
                      $gt = floor($amt1*100)/100;
                      //$gt = number_format($amt1,2);
                     $value =  '"'.$invoiceDate .'","VE","0","'.$code.'","'.$customer_name.'",'.$gt.',"","'.$invoice->getIncrementId().'"';
                  }
                  elseif($from == "USD")
                  {
                      $amt1 = $order->getGrandTotal()*$divided_amt;
                      //$amt1 = $final_total;
                      //$gt = floor($amt1*100)/100;
                      $gt = number_format($amt1,2);
                     $value =  '"'.$invoiceDate .'","VE","0","'.$code.'","'.$customer_name.'",'.$gt.',"","'.$invoice->getIncrementId().'"'; 
                  }
                  else
                  {
                      $value =  '"'.$invoiceDate .'","VE","0","'.$code.'","'.$customer_name.'","'.number_format($order->getGrandTotal(),2).'","","'.$invoice->getIncrementId().'"';
                  }
                   @fwrite($fp, $value."\n");
                   //echo "<pre>";print_r($tvaDetail);die();
                  foreach ($tvaDetail as $tvaItem ) {
                      
                               //subtotal
                               //rebate
                              if($order->getMarketplacesPartnerCode() == 'rueducommerce' || $payment->getMethod() == 'rueducommerce')
                              {
                                   $code_subtotal = '70762000';
                                   $code_rebate = '70970762';

                              }
                              else if($order->getMarketplacesPartnerCode() == 'pixmania' || $payment->getMethod() == 'pixmania')
                              {
                                   $code_subtotal = '70761000';
                                   $code_rebate = '70970761';

                              }
                              else if($order->getShippingAddress()->getCountry_id() == 'FR' || $order->getShippingAddress()->getCountry_id() == 'MC' || $order->getShippingAddress()->getCountry_id() == 'FX')
                                 {
                                    if(number_format($tvaItem ['pTva'],2) == '20')
                                     {
                                        $code_subtotal = '70710000';
                                        $code_rebate = '70970710';
                                     }
                                     else if(number_format($tvaItem ['pTva'],2) == '5.5')
                                     {
                                       $code_subtotal = '70715000';
                                       $code_rebate = '70970715';
                                     }
                                     
                                     

                                 }
                                 
                                 else if($order->getShippingAddress()->getCountry_id() == 'DE' ||
                                         $order->getShippingAddress()->getCountry_id() == 'AT' ||
                                         $order->getShippingAddress()->getCountry_id() == 'BE' ||
                                         $order->getShippingAddress()->getCountry_id() == 'BG' ||
                                         $order->getShippingAddress()->getCountry_id() == 'CY' ||
                                         $order->getShippingAddress()->getCountry_id() == 'DK' ||
                                         $order->getShippingAddress()->getCountry_id() == 'ES' ||
                                         $order->getShippingAddress()->getCountry_id() == 'EE' ||
                                         $order->getShippingAddress()->getCountry_id() == 'FI' ||
                                         $order->getShippingAddress()->getCountry_id() == 'GR' ||
                                         $order->getShippingAddress()->getCountry_id() == 'HU' ||
                                         $order->getShippingAddress()->getCountry_id() == 'IE' ||
                                         $order->getShippingAddress()->getCountry_id() == 'IT' ||
                                         $order->getShippingAddress()->getCountry_id() == 'LV' ||
                                         $order->getShippingAddress()->getCountry_id() == 'LT' ||
                                         $order->getShippingAddress()->getCountry_id() == 'LU' ||
                                         $order->getShippingAddress()->getCountry_id() == 'MT' ||
                                         $order->getShippingAddress()->getCountry_id() == 'NL' ||
                                         $order->getShippingAddress()->getCountry_id() == 'PL' ||
                                         $order->getShippingAddress()->getCountry_id() == 'PT' ||
                                         $order->getShippingAddress()->getCountry_id() == 'RO' ||
                                         $order->getShippingAddress()->getCountry_id() == 'GB' ||
                                         $order->getShippingAddress()->getCountry_id() == 'SK' ||
                                         $order->getShippingAddress()->getCountry_id() == 'SI' ||
                                         $order->getShippingAddress()->getCountry_id() == 'SE' ||
                                         $order->getShippingAddress()->getCountry_id() == 'CZ'
                                 )
                                 {
                                     if(number_format($tvaItem ['pTva'],2) == '20')
                                     {
                                        $code_subtotal = '70740000';
                                        $code_rebate = '70970740';
                                     }
                                     else if(number_format($tvaItem ['pTva'],2) == '5.5')
                                     {
                                        $code_subtotal = '70745000';
                                        $code_rebate = '70970745';
                                     }
                                     else if(number_format($tvaItem ['pTva'],2) == '0.0000')
                                     {
                                        $code_subtotal = '70770000';
                                        $code_rebate = '70970770';
                                     }
                                 }
                                 else
                                 {

                                    $code_subtotal = '70750000';
                                    $code_rebate = '70970750';
                                 }


                                 //shipping cost

                                if($order->getShippingAddress()->getCountry_id() == 'FR' || $order->getShippingAddress()->getCountry_id() == 'MC' || $order->getShippingAddress()->getCountry_id() == 'FX')
                                {
                                   $code_shipping = '70850000';
                                }
                                else if($order->getShippingAddress()->getCountry_id() == 'DE' ||
                                           $order->getShippingAddress()->getCountry_id() == 'AT' ||
                                           $order->getShippingAddress()->getCountry_id() == 'BE' ||
                                           $order->getShippingAddress()->getCountry_id() == 'BG' ||
                                           $order->getShippingAddress()->getCountry_id() == 'CY' ||
                                           $order->getShippingAddress()->getCountry_id() == 'DK' ||
                                           $order->getShippingAddress()->getCountry_id() == 'ES' ||
                                           $order->getShippingAddress()->getCountry_id() == 'EE' ||
                                           $order->getShippingAddress()->getCountry_id() == 'FI' ||
                                           $order->getShippingAddress()->getCountry_id() == 'GR' ||
                                           $order->getShippingAddress()->getCountry_id() == 'HU' ||
                                           $order->getShippingAddress()->getCountry_id() == 'IE' ||
                                           $order->getShippingAddress()->getCountry_id() == 'IT' ||
                                           $order->getShippingAddress()->getCountry_id() == 'LV' ||
                                           $order->getShippingAddress()->getCountry_id() == 'LT' ||
                                           $order->getShippingAddress()->getCountry_id() == 'LU' ||
                                           $order->getShippingAddress()->getCountry_id() == 'MT' ||
                                           $order->getShippingAddress()->getCountry_id() == 'NL' ||
                                           $order->getShippingAddress()->getCountry_id() == 'PL' ||
                                           $order->getShippingAddress()->getCountry_id() == 'PT' ||
                                           $order->getShippingAddress()->getCountry_id() == 'RO' ||
                                           $order->getShippingAddress()->getCountry_id() == 'GB' ||
                                           $order->getShippingAddress()->getCountry_id() == 'SK' ||
                                           $order->getShippingAddress()->getCountry_id() == 'SI' ||
                                           $order->getShippingAddress()->getCountry_id() == 'SE' ||
                                           $order->getShippingAddress()->getCountry_id() == 'CZ')
                                       {
                                          if(number_format($tvaItem ['pTva'],2) == '0.0000')
                                          {
                                          $code_shipping = '70857000';
                                          }
                                          else
                                          {
                                          $code_shipping = '70854000';
                                          }
                                       }
                                       else
                                       {
                                         $code_shipping = '70855000';
                                       }

                                       //vat
                                       
                                      if(number_format($tvaItem ['pTva'],2) == '20')
                                      {
                                          $code_vat = '44571999';
                                          $code_vat1 = 'ECO TAX';
                                      }
                                      else if(number_format($tvaItem ['pTva'],2) == '19.6')
                                      {
                                          $code_vat = '44571000';
                                      }
                                      else if(number_format($tvaItem ['pTva'],2) == '5.5')
                                      {
                                          $code_vat = '44571100';
                                      }
                                      else if(number_format($tvaItem ['pTva'],2) == '2.1')
                                      {
                                          $code_vat = '44571200';
                                      }
									  
                                     if($tvaItem['sstotht']!='0.0000')
                                     {
                                       
                                       $value =  '"'.$invoiceDate .'","VE","0","'.$code_subtotal.'","'.$customer_name.'","","'.number_format($tvaItem['sstotht'],2).'","'.$invoice->getIncrementId().'"';
                                       @fwrite($fp, $value."\n");
                                     }

                                     if($tvaItem['port']!='0.0000')
                                     {
                                        $value =  '"'.$invoiceDate .'","VE","0","'.$code_shipping.'","'.$customer_name.'","","'.number_format($tvaItem['port'],2).'","'.$invoice->getIncrementId().'"';
                                        @fwrite($fp, $value."\n");
                                     }

                                     if($tvaItem['tva']!='0.0000')
                                     {
                                       $value =  '"'.$invoiceDate .'","VE","0","'.$code_vat.'","'.$customer_name.'","","'.number_format($invoice->getBaseTaxAmount(),2).'","'.$invoice->getIncrementId().'"';
                                       @fwrite($fp, $value."\n");
                                     }
                                     if($tvaItem['dee'] == 0)
                                     {
                                         unset($tvaItem['dee']);
                                     }
                                     else
                                     {
                                       $value =  '"'.$invoiceDate .'","VE","0","'.$code_vat1.'","'.$customer_name.'","","'.number_format($tvaItem['dee'],2).'","'.$invoice->getIncrementId().'"';
                                       @fwrite($fp, $value."\n");
                                     }
                                     if($tvaItem['rem']!='0.0000')
                                     {
                                       $value =  '"'.$invoiceDate .'","VE","0","'.$code_rebate.'","'.$customer_name.'","'.number_format($tvaItem['rem'],2).'","","'.$invoice->getIncrementId().'"';
                                       @fwrite($fp, $value."\n");
                                     }
                                     
                                }
                                
                               
                       //end foreach
         }

         fclose($fp);
         
         $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));

    
	}
}
