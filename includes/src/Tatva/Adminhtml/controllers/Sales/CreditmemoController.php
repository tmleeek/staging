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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales orders controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once 'Mage/Adminhtml/controllers/Sales/CreditmemoController.php';
class Tatva_Adminhtml_Sales_CreditmemoController extends Mage_Adminhtml_Sales_CreditmemoController
{
   //create by nisha for export account accuracy
    public function exportaccountAction(){
      $creditmemoIds = $this->getRequest()->getPost('creditmemo_ids');
      $path = Mage::getBaseDir('var') . DS . 'export' . DS;
      if(file_exists(Mage::getBaseDir('var') . DS . 'export' . DS. 'exportaccounting-creditmemo.csv'))
      {
        unlink(Mage::getBaseDir('var') . DS . 'export' . DS. 'exportaccounting-creditmemo.csv');
      }
      $file = 'exportaccounting-creditmemo.csv';
      $fp = @fopen($path.$file, 'w');

      for($i=0;$i<count($creditmemoIds);$i++)
      {
                  $creditmemo = Mage::getModel("sales/order_creditmemo")->load($creditmemoIds[$i]);


                  $order_id = $creditmemo->getOrderId();

                  $order = Mage::getModel('sales/order')->load($order_id);
                  $total = array();
                         $from = $order->getOrderCurrencyCode();
                         $to = $order->getGlobalCurrencyCode();    
                         $discount = floatval($order->getBaseDiscountAmount());
                  $tvaDetail = array ();
            		foreach ( $creditmemo->getAllItems () as $item ) {
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
            		if ($creditmemo->getShippingAmount () > 0) {
            			$codeTva = 't' . ($order->getPercentTaxShipping () * 1);
            			$tvaDetail [$codeTva] = array (
            					'sstotht' => 0,
            					'rem' => 0,
            					'port' => 0,
            					'pTva' => 0,
            					'tva' => 0,
            					'totht' => 0 );

            		}
                         $tvaDetail [$codeTva] ['sstotht'] = 0;
                        $tvaDetail [$codeTva] ['tva'] = 0;
                        $tvaDetail [$codeTva] ['dee'] = 0;
                        $weee_amt = 0;
            		foreach ( $creditmemo->getAllItems () as $item ) {
            			$_product = Mage::getModel('catalog/product')->load($item->getProductId());
			    
            			$orderItem = $item->getOrderItem ();
            			 $codeTva = 't' . ($orderItem->getTaxPercent () * 1);
            			$tvaDetail [$codeTva] ['pTva'] = $orderItem->getTaxPercent ();
                                $tvaDetail [$codeTva] ['sstotht'] += $item->getBaseRowTotal();
                                /*if($from == "USD")
                                {
                                   $tvaDetail [$codeTva] ['sstotht'] = $this->getsstotht($from,$tvaDetail [$codeTva] ['sstotht'],$to);
                                   $tvaDetail [$codeTva] ['sstotht'] += $tvaDetail [$codeTva] ['sstotht'];
                                }*/
                                
                                
                               //echo $tvaDetail [$codeTva] ['tva'] += $item->getBaseTaxAmount();
                               
                               
                                $tvaDetail [$codeTva] ['totht'] = $tvaDetail [$codeTva] ['totht'] + $item->getRowTotal ()  - $orderItem->getDiscountAmount ();
            			$tvaDetail [$codeTva] ['rem'] = $tvaDetail [$codeTva] ['rem'] + $item->getDiscountAmount ();
                                
                                
                                  $tvaDetail [$codeTva] ['dee'] += Mage::helper('weee')->getAmount($_product);
                                // $tvaDetail [$codeTva] ['sstotht'] = round($tvaDetail [$codeTva] ['sstotht'] - $weee_amt,2);
                                
                                //$tvaDetail [$codeTva] ['sstotht'] = round($tvaDetail [$codeTva] ['sstotht'], 2);
                                
                                
                                
                                //array_push($total,$tvaDetail [$codeTva] ['dee']);

            		

                    if ($creditmemo->getBaseShippingAmount () > 0) {
            			$orderItem = $item->getOrderItem ();
            			$codeTva = 't' . ($orderItem->getTaxPercent () * 1);
            			$tvaDetail [$codeTva] ['pTva'] = $orderItem->getTaxPercent ();
            			
                                $tvaDetail [$codeTva] ['port'] = $creditmemo->getBaseShippingAmount();
            		}
                    }
                    $tvaDetail [$codeTva] ['port'] = $order->getBaseShippingAmount();
                        $tvaDetail [$codeTva] ['tva'] = $order->getBaseTaxAmount();
                        if($from == "USD")
                                {
                                    $tvaDetail [$codeTva] ['sstotht'] = $this->getsstotht($from,$tvaDetail [$codeTva] ['sstotht'],$to);
                                }
                     if($discount==0)
                    {
                         //echo "test";die();
                        $tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht']-$tvaDetail [$codeTva] ['dee'];
                    }
                    
                        if($from == "USD")
                                {
                                    $tvaDetail [$codeTva] ['tva'] = $this->gettva($from,$tvaDetail [$codeTva] ['tva'],$to);
                                }
                        if($from=="USD")
                                {
                                    $tvaDetail [$codeTva] ['port'] = $this->getport($from,$invoice->getShippingAmount(),$to);
                                }        
                        array_push($total,($tvaDetail [$codeTva] ['sstotht']),$tvaDetail [$codeTva] ['tva'],$tvaDetail [$codeTva] ['port'],$tvaDetail [$codeTva] ['dee']);
                        
                        //echo "<pre>";print_r($total);die();
                        
                    $final_total = array_sum($total);
                    //echo $discount;
                    if($discount==0)
                    {
                      $final_total=$final_total;
                       
                    }
                    else
                    {
                        $final_total=$final_total + $discount;
                    }
                  $code = '';
                  $code_subtotal = '';
                  $code_shipping = '';
                  $code_rebate = '';
                  $code_vat = '';

                  $payment = $order->getPayment();



                    //echo 'hello=='.$order->getShippingAddress()->getCountry_id(); exit;
                    //echo '<pre>';
                    //print_r($payment->getData());
                  //exit;
                  $date = $creditmemo->getCreatedAt();

                  $creditmemoDate = date("d/m/Y", strtotime($date));
                   //echo $payment->getMethod();exit;
                  //payment type
                  if($payment->getMethod() == 'transferpayment')
                  {
                      $code = '9VRMT';
                  }
                  else if($payment->getMethod() == 'atos_standard' || $payment->getMethod() == 'cybermut_payment' || $payment->getMethod() == 'cybermutforeign_payment')
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
				  else if($payment->getMethod() == 'paypal_standard')
                  {
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




                  $value = '';
                  $customer_name = iconv('UTF-8','UCS-2LE',$order->getCustomerFirstname().' '.$order->getCustomerLastname());
                  //$value =  '"'.$creditmemoDate .'","VE","0","'.$code.'","'.$customer_name.'","","'.number_format($order->getTotalRefunded(),2).'","'.$creditmemo->getIncrementId().'"';
                   if($from=="GBP")
                  {
                     // echo $order->getGrandTotal();die();
                     // $amt1 = $order->getGrandTotal()/$divided_amt;
                      $amt1 = $final_total;
                      //$gt = floor($amt1*100)/100;
                      $gt = number_format($amt1,2);
                      $value =  '"'.$creditmemoDate .'","VE","0","'.$code.'","'.$customer_name.'","","'.$gt.'","'.$creditmemo->getIncrementId().'"';
                  }
                  elseif($from == "USD")
                  {
                      //$amt1 = $order->getGrandTotal()*$divided_amt;
                      $amt1 = $final_total;
                      //$gt = floor($amt1*100)/100;
                      $gt = number_format($amt1,2);
                     $value =  '"'.$creditmemoDate .'","VE","0","'.$code.'","'.$customer_name.'","","'.$gt.'","'.$creditmemo->getIncrementId().'"'; 
                  }
                  else
                  {
                      $amt1 = $final_total;
                      $gt = number_format($amt1,2);
                      //$value =  '"'.$invoiceDate .'","VE","0","'.$code.'","'.$customer_name.'","'.number_format($order->getGrandTotal(),2).'","","'.$invoice->getIncrementId().'"';
                      $value =  '"'.$creditmemoDate .'","VE","0","'.$code.'","'.$customer_name.'","","'.$gt.'","'.$creditmemo->getIncrementId().'"';
                  }
                   @fwrite($fp, $value."\n");
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
                                       $address = $order->getShippingAddress()->getData();
                                        //echo $address['country_id'];die();
                                        $eu_countries = Mage::getStoreConfig('general/country/eu_countries');
                                        $eu_countries_array = explode(',',$eu_countries);
                                        if(in_array($address['country_id'], $eu_countries_array) && $address['country_id']!="FR"){
                                            $code_vat1 = '70794000';
                                        }
                                        elseif($address['country_id']=="FR")
                                        {
                                            $code_vat1 = '70791000';
                                        }
                                        else
                                        {
                                            $code_vat1 = '70795000';
                                        }
                                     if($tvaItem['sstotht']!='0.0000')
                                     {
                                       $value =  '"'.$creditmemoDate .'","VE","0","'.$code_subtotal.'","'.$customer_name.'","'.number_format($tvaItem['sstotht'],2).'","","'.$creditmemo->getIncrementId().'"';
                                       @fwrite($fp, $value."\n");
                                     }
                                     
                                     

                                     if($tvaItem['tva']!='0.0000')
                                     {
                                       $value =  '"'.$creditmemoDate .'","VE","0","'.$code_vat.'","'.$customer_name.'","'.number_format($tvaItem['tva'],2).'","","'.$creditmemo->getIncrementId().'"';
                                       @fwrite($fp, $value."\n");
                                     }
                                     if($tvaItem['port']!='0.0000')
                                     {
                                        $value =  '"'.$creditmemoDate .'","VE","0","'.$code_shipping.'","'.$customer_name.'","'.number_format($tvaItem['port'],2).'","","'.$creditmemo->getIncrementId().'"';
                                        @fwrite($fp, $value."\n");
                                     }
                                     if($tvaItem['dee'] == 0)
                                     {
                                         unset($tvaItem['dee']);
                                     }
                                     else
                                     {
                                       $value =  '"'.$creditmemoDate .'","VE","0","'.$code_vat1.'","'.$customer_name.'","","'.number_format($tvaItem['dee'],2).'","'.$creditmemo->getIncrementId().'"';
                                       @fwrite($fp, $value."\n");
                                     }
                                     if($tvaItem['rem']!='0.0000')
                                     {
                                       $value =  '"'.$creditmemoDate .'","VE","0","'.$code_rebate.'","'.$customer_name.'","","'.number_format($tvaItem['rem'],2).'","'.$creditmemo->getIncrementId().'"';
                                       @fwrite($fp, $value."\n");
                                     }
                                }


                       //end foreach
         }

         fclose($fp);
         $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));

        }
        public function gettva($from,$tva,$to)
        {
            if($from!="EUR")
            {
                $ammount = number_format(Mage::helper('directory')->currencyConvert($tva, $from, $to),2);
            }
            else
            {
                $ammount = $tva;
            }
            return $ammount;
        }
        public function getport($from,$port,$to)
        {
            if($from!="EUR")
            {
                $ammount = number_format(Mage::helper('directory')->currencyConvert($port, $from, $to),2);
            }
            else
            {
                $ammount = $port;
            }
            return $ammount;
        }
        public function getsstotht($from,$sstotht,$to)
        {
            if($from!="EUR")
            {
                $ammount = number_format(Mage::helper('directory')->currencyConvert($sstotht, $from, $to),2);
            }
            else
            {
                $ammount = $sstotht;
            }
            return $ammount;
        }
}