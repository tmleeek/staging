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
    public function exportaccountAction()
    {

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
            //print_r($creditmemoIds);
            //exit;
            $creditmemo = Mage::getModel("sales/order_creditmemo")->load($creditmemoIds[$i]);

            $order_id = $creditmemo->getOrderId();

            $order = Mage::getModel('sales/order')->load($order_id);

            $order_inc_id = $order->getIncrementId();
            $tvaDetail = array ();
            foreach ($creditmemo->getAllItems () as $item )
            {
                $orderItem = $item->getOrderItem ();
                $parent_id = $orderItem->getParentItemId();
                if(empty($parent_id))
                {
                    if(!empty($orderItem))
                    {
                       $codeTva = 't' . ($orderItem->getTaxPercent () * 1);
                	    $tvaDetail [$codeTva] = array (
                	    'sstotht' => 0,
                		'rem' => 0,
                		'port' => 0,
                		'pTva' => 0,
                		'tva' => 0,
                		'totht' => 0,
                        'dee' => 0 );

                    }

                }
            }

            /*if ($creditmemo->getShippingAmount () > 0)
            {
                $codeTva = 't' . ($order->getPercentTaxShipping () * 1);
            	$tvaDetail [$codeTva] = array(
            		'sstotht' => 0,
            		'rem' => 0,
            		'port' => 0,
            		'pTva' => 0,
            		'tva' => 0,
            	    'totht' => 0,
                    'dee' => 0);
            }*/

            //echo "<pre>";print_r($order->getData());//exit;
            //echo "<pre>";print_r($creditmemo->getData());die();
            $item_vat = array();
            $from = $order->getOrderCurrencyCode();
            $store_data = Mage::getModel('core/store')->load($order->getStoreId());
            $orderSotreName = $store_data->getName();
            $shipping_method_name = $order->getShippingMethod();


            if(empty($shipping_method_name))
            {
                $orderCountryName = $order->getBillingAddress()->getCountryId();
            }
            else
            {
               $orderCountryName = $order->getShippingAddress()->getCountryId();
            }

            $divided_amt = number_format($order->getStoreToOrderRate(),2);
            $baseamount_refnded = $creditmemo->getGrandTotal();
            $percent_tax_shipping = $order->getPercentTaxShipping();
            $currency_data = '';
            $tvaShipping = '';

            if($from == "USD")
            {
                $currency_data = 'VUS';
            }
            else if($from == "GBP")
            {
                $currency_data = 'VUK';
            }
            else if($from == "CHF")
            {
                $currency_data = 'VCH';
            }
            else if($from == "EUR")
            {
                $currency_data = 'VE';
            }
            else
            {
               $currency_data = 'VOT';
            }

            foreach($creditmemo->getAllItems () as $item )
            {
                $_product = Mage::getModel('catalog/product')->load($item->getProductId());
			    $weee_amt = Mage::helper('weee')->getAmount($_product);

                $orderItem = $item->getOrderItem ();
                $parent_id = $orderItem->getParentItemId();

                if(!empty($parent_id))
                {
                    $tax_percentage =  $item_vat[$parent_id];
                }
                else
                {
                    $order_item_id = $orderItem->getItemId();
                    $item_vat[$order_item_id]= $orderItem->getTaxPercent();
                    $tax_percentage =  $item_vat[$order_item_id];
                }

            	$codeTva = 't' . ($tax_percentage * 1);
                $tvaDetail [$codeTva] ['pTva'] = $tax_percentage;

                //echo "<pre>";print_r($orderItem->getData()); echo "</pre>";

                if($from=="GBP")
                {
                    $tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] + $item->getRowTotal ();
                }
                elseif($from=="USD")
                {
                    $tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] + $item->getRowTotal ();
                }
                else
                {
                    $tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] + $item->getRowTotal ();
                }

                $tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $item->getTaxAmount ();
            	$tvaDetail [$codeTva] ['totht'] = $tvaDetail [$codeTva] ['totht'] + $item->getRowTotal ()  - $orderItem->getDiscountAmount ();
            	$tvaDetail [$codeTva] ['rem'] = $tvaDetail [$codeTva] ['rem'] + $item->getDiscountAmount ();

                if(isset($weee_amt) && $weee_amt!=0)
                {
                    if($tvaDetail [$codeTva] ['dee'] == '0')
                    {
                        $tvaDetail [$codeTva] ['dee'] = $weee_amt * $item->getQty();
                    }
                    else
                    {
                        $tvaDetail [$codeTva] ['dee'] = ($weee_amt * $item->getQty()) + $tvaDetail [$codeTva] ['dee'];
                    }
                }
                /*else
                {
                    $tvaDetail [$codeTva] ['dee'] = 0;
                }*/
            }
            //exit;
            if(!empty($codeTva))
            {
                $tvaDetail [$codeTva] ['sstotht'] = $tvaDetail [$codeTva] ['sstotht'] - $tvaDetail [$codeTva] ['dee'];
                $total_check = $tvaDetail [$codeTva] ['totht'] + $tvaDetail [$codeTva] ['tva'] + $tvaDetail [$codeTva] ['rem'] + $tvaDetail [$codeTva] ['port'] + $tvaDetail [$codeTva] ['dee'];
            }

            if ($creditmemo->getShippingAmount () > 0)
            {
                $item_percentage = $tvaDetail [$codeTva] ['pTva'];
                /*if($item_percentage == "0")
                    $item_percentage = $order->getPercentTaxShipping();*/

                $codeTva = 't' . ($item_percentage * 1);
                //$tvaDetail [$codeTva] ['pTva'] = $item_percentage;

                if($from=="GBP")
                {
                    //echo "GBP";
                    $tvaDetail [$codeTva] ['port'] = $creditmemo->getShippingAmount();
                    $VAT = $creditmemo->getShippingAmount ();
                }
                elseif($from=="USD")
                {
                    //echo "USD";
                    $tvaDetail [$codeTva] ['port'] = $creditmemo->getShippingAmount ();
                    $VAT = $creditmemo->getShippingAmount ();
                }
                else
                {
                    //echo "else";
                    $tvaDetail [$codeTva] ['port'] = $creditmemo->getShippingAmount ();
                    $VAT = $creditmemo->getShippingAmount();
                }

            	$tvaShipping = $creditmemo->getShippingTaxAmount ();
                //$tvaDetail [$codeTva] ['tva'] = $tvaDetail [$codeTva] ['tva'] + $tvaShipping;
                //$total_check = $tvaDetail [$codeTva] ['totht'] + $tvaDetail [$codeTva] ['tva'] + $tvaDetail [$codeTva] ['rem'] + $tvaDetail [$codeTva] ['port'] + $tvaDetail [$codeTva] ['dee'];

                //array_push($tvaDetail [$codeTva] ['sstotht'],$tvaDetail [$codeTva] ['port'],$tvaDetail [$codeTva] ['tva'],$tvaDetail [$codeTva] ['dee']);
            }
           $total_baseamount_check = $baseamount_refnded + 0.01;

           //echo "baseamount_invoiced =".$baseamount_refnded;
           //echo "<br />";

            /*echo "<pre>";
            print_r($tvaDetail);
            echo "</pre>";
            exit;*/
            //$final_total = array_sum($total);

            $code = '';
            $code_subtotal = '';
            $code_shipping = '';
            $code_rebate = '';
            $code_vat = '';
            $code_dee = '';
            $amazone = '0';
            $ebay = '0';
            $payment = $order->getPayment();

            //echo '<pre>';print_r($payment->getData());exit;
			if($payment->getAdditionalData())
			{
                $payment_arr = unserialize($payment->getAdditionalData());
                //echo '<pre>';print_r($payment_arr);exit;
				if($payment_arr['component_mode'] == 'amazon')
				{
                    $amazone = '1';
				}
                else if($payment_arr['component_mode'] == 'ebay')
                {
                     $ebay = '1';
                }

			}

            if($amazone == '1')
            {
                if($orderSotreName == "Amazon UK")
                {
                    $code = '9AMAZUK';
                }
                else if($orderSotreName == "Amazon US")
                {
                    $code = '9AMAZUS';
                }
                else if($orderSotreName == "Amazon Allemagne FBA")
                {
                    $code = '9AMAZDE';
                }
                else if($orderSotreName == "Amazon ES")
                {
                    $code = '9AMAZES';
                }
                else if($orderSotreName == "Amazon IT")
                {
                    $code = '9AMAZIT';
                }
                else
                {
                    $code = '9AMAZON';
                }
            }
            else if($payment->getMethod() == 'mandatcash')
            {
                $code = '9MANDAT';
            }
            else if($payment->getMethod() == 'checkmo')
            {
                $code = '9CHEQUE';
            }
            else if($order->getMarketplacesPartnerCode() == 'rueducommerce' || $payment->getMethod() == 'rueducommerce')
            {
                $code = '9RDC';
            }
            else if($order->getMarketplacesPartnerCode() == 'pixmania' || $payment->getMethod() == 'pixmania')
            {
                $code = '9PIX';
            }
            else if($order->getMarketplacesPartnerCode() == 'fbd' || $payment->getMethod() == 'fbd')
            {
                $code = '9FDB';
            }
            else if($order->getFromSite() == 'cdiscount')
            {
                $code = '9CDISCOU';
            }
            else if($payment->getMethod() == 'priceminister')
            {
                $code = '9PRICE';
            }
            else if($payment->getMethod() == 'mandatadministratif')
            {
                $code = '9MA';
            }
            else if($from=="USD" && $orderSotreName == "US")
            {
                if($payment->getMethod() == 'transferpayment')
                {
                    $code = '9VRMTUS';
                }
                else if($payment->getMethod() == 'atos_standard')
                {
                    $code = '9CBUS';
                }
                else if($payment->getMethod() == 'cybermut_payment')
                {
                    $code = '9CBUS';
                }
                else if($payment->getMethod() == "cybermutforeign_payment")
                {
                    $code = '9CBUS';
                }
                else if($payment->getMethod() == 'paypal_standard' || $payment->getMethod() == "paypal_billing_agreement" || $payment->getMethod() == "paypal_mecl")
                {
                    $code = '9PAYUS';
                }
                else if($payment->getMethod() == "m2epropayment")
                {
                    $code = '9PAYUS';
                }
            }
            else if($from=="GBP" && $orderSotreName == "GB")
            {
                if($payment->getMethod() == 'transferpayment')
                {
                    $code = '9VRMTUK';
                }
                else if($payment->getMethod() == 'atos_standard')
                {
                    $code = '9CBUK';
                }
                else if($payment->getMethod() == 'cybermut_payment')
                {
                    $code = '9CBUK';
                }
                else if($payment->getMethod() == "cybermutforeign_payment")
                {
                    $code = '9CBUK';
                }
                else if($payment->getMethod() == 'paypal_standard' || $payment->getMethod() == "paypal_billing_agreement" || $payment->getMethod() == "paypal_mecl")
                {
                    $code = '9PAYUK';
                }
                else if($payment->getMethod() == "m2epropayment")
                {
                    $code = '9PAYUK';
                }
            }
            else if($from=="CHF" && $orderSotreName == "CH")
            {
                if($payment->getMethod() == 'transferpayment')
                {
                    $code = '9VRMTCHF';
                }
                else if($payment->getMethod() == 'atos_standard')
                {
                    $code = '9CBCHF';
                }
                else if($payment->getMethod() == 'cybermut_payment')
                {
                    $code = '9CBCHF';
                }
                else if($payment->getMethod() == "cybermutforeign_payment")
                {
                    $code = '9CBCHF';
                }
                else if($payment->getMethod() == 'paypal_standard' || $payment->getMethod() == "paypal_billing_agreement" || $payment->getMethod() == "paypal_mecl")
                {
                    $code = '9PAYCHF';
                }
                else if($payment->getMethod() == "m2epropayment")
                {
                    $code = '9PAYCHF';
                }
            }
            else
            {
                if($payment->getMethod() == 'transferpayment')
                {
                    $code = '9VRMT';
                }
                else if($payment->getMethod() == 'atos_standard')
                {
                    $code = '9CB';
                }
                else if($payment->getMethod() == 'cybermut_payment')
                {
                    $code = '9CB';
                }
                else if($payment->getMethod() == "cybermutforeign_payment")
                {
                    $code = '9CB';
                }
                else if($payment->getMethod() == 'paypal_standard' || $payment->getMethod() == "paypal_billing_agreement" || $payment->getMethod() == "paypal_mecl")
                {
                    $code = '9PAYPAL';
                }
                else if($payment->getMethod() == "m2epropayment")
                {
                    $code = '9PAYPAL';
                }
            }
            //echo $code;
            //exit;
            $date = $creditmemo->getCreatedAt();
            $creditmemoDate = date("d/m/Y", strtotime($date));

            //subtotal
            $value = '';
            $customer_name = $order->getCustomerFirstname().' '.$order->getCustomerLastname();


            if($from=="GBP")
            {
                if($amazone == '1' || $ebay == '1')
                {
                   $gt = $baseamount_refnded;
                   $gt = number_format($gt,2,'.','');
                }
                else
                {
                    $amt1 = $baseamount_refnded/$divided_amt;
                    $gt = ($amt1*100)/100;
                    $gt = number_format($gt,2,'.','');
                }

               $value =  '"'.$creditmemoDate .'","'.$orderSotreName.'","VE","0","'.$code.'","'.$customer_name.'","'.$orderCountryName.'","'.$gt.'","","'.$from.'","'.$currency_data.'","'.$creditmemo->getIncrementId().'","'.$order_inc_id.'"';
            }
            elseif($from == "USD")
            {
                if($amazone == '1' || $ebay == '1')
                {
                   $gt = $baseamount_refnded;
                   $gt = number_format($gt,2,'.','');
                }
                else
                {
                    $amt1 = $baseamount_refnded*$divided_amt;
                    $gt = number_format($amt1,2,'.','');
                }
               $value =  '"'.$creditmemoDate .'","'.$orderSotreName.'","VE","0","'.$code.'","'.$customer_name.'","'.$orderCountryName.'","'.$gt.'","","'.$from.'","'.$currency_data.'","'.$creditmemo->getIncrementId().'","'.$order_inc_id.'"';
            }
            else
            {
                $value =  '"'.$creditmemoDate .'","'.$orderSotreName.'","VE","0","'.$code.'","'.$customer_name.'","'.$orderCountryName.'","'.number_format($baseamount_refnded,2,'.','').'","","'.$from.'","'.$currency_data.'","'.$creditmemo->getIncrementId().'","'.$order_inc_id.'"';
            }

            @fwrite($fp, $value."\n");

            //echo "<pre>";print_r($value);echo "</pre>";

            foreach ($tvaDetail as $tvaItem )
            {
                //subtotal
                if($order->getMarketplacesPartnerCode() == 'rueducommerce' || $payment->getMethod() == 'rueducommerce')
                {
                     $code_subtotal = '70710010';
                }
                else if($order->getMarketplacesPartnerCode() == 'pixmania' || $payment->getMethod() == 'pixmania')
                {
                     $code_subtotal = '70711030';
                }
                else if($order->getMarketplacesPartnerCode() == 'fbd' || $payment->getMethod() == 'fbd')
                {
                     $code_subtotal = '70710030';
                }
                else if($order->getFromSite() == 'cdiscount')
                {
                    $code_subtotal = '70710040';
                }
                else if($payment->getMethod() == 'priceminister')
                {
                    $code_subtotal = '70710050';
                }
                else if($amazone == '1')
			    {
			        if($orderCountryName == 'FR')
                    {
                       $code_subtotal = '70710021';
                    }
                    else if($orderCountryName == 'DE')
                    {
                       $code_subtotal = '70740031';
                    }
                    else if($orderCountryName == 'IT')
                    {
                       $code_subtotal = '70740041';
                    }
                    else if($orderCountryName == 'ES')
                    {
                       $code_subtotal = '70740051';
                    }
                    else if($orderCountryName == 'GB')
                    {
                       $code_subtotal = '70740021';
                    }
                    else if($orderCountryName == 'US')
                    {
                       $code_subtotal = '70750011';
                    }
                    else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                    {
                       $code_subtotal = '70740090';
                    }
                    else
                    {
                        $code_subtotal = '70750090';
                    }

                }
                else if($ebay == '1')
			    {
			        if($orderCountryName == 'FR')
                    {
                       $code_subtotal = '70710022';
                    }
                    else if($orderCountryName == 'DE')
                    {
                       $code_subtotal = '70740032';
                    }
                    else if($orderCountryName == 'IT')
                    {
                       $code_subtotal = '70740042';
                    }
                    else if($orderCountryName == 'ES')
                    {
                       $code_subtotal = '70740052';
                    }
                    else if($orderCountryName == 'GB')
                    {
                       $code_subtotal = '70740022';
                    }
                    else if($orderCountryName == 'US')
                    {
                       $code_subtotal = '70750012';
                    }
                    else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                    {
                       $code_subtotal = '70740095';
                    }
                    else
                    {
                        $code_subtotal = '70750095';
                    }
                }
                else if($orderCountryName == 'FR' || $orderCountryName == 'MC' || $orderCountryName == 'AD')
                {
                    if(number_format($tvaItem ['pTva'],2) == '20')
                    {
                        $code_subtotal = '70710000';
                    }
                    else if(number_format($tvaItem ['pTva'],2) == '5.5')
                    {
                        $code_subtotal = '70715000';
                    }
                    else
                    {
                        $code_subtotal = '70750000';
                    }
                }
                else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                {
                    if(number_format($tvaItem ['pTva'],2) == '0.0000')
                    {
                        $code_subtotal = '70770000';
                    }
                    else
                    {
                       $code_subtotal = '70740000';
                    }
                }
                else if($orderCountryName == 'GB')
                {
                    if(number_format($tvaItem ['pTva'],2) == '0.0000')
                    {
                        $code_subtotal = '70770000';
                    }
                    else
                    {
                        $code_subtotal = '70740020';
                    }
                }
                else if($orderCountryName == 'DE')
                {
                    if(number_format($tvaItem ['pTva'],2) == '0.0000')
                    {
                        $code_subtotal = '70770000';
                    }
                    else
                    {
                        $code_subtotal = '70740030';
                    }
                }
                else if($orderCountryName == 'IT')
                {
                    if(number_format($tvaItem ['pTva'],2) == '0.0000')
                    {
                        $code_subtotal = '70770000';
                    }
                    else
                    {
                        $code_subtotal = '70740040';
                    }
                }
                else if($orderCountryName == 'ES')
                {
                    if(number_format($tvaItem ['pTva'],2) == '0.0000')
                    {
                        $code_subtotal = '70770000';
                    }
                    else
                    {
                        $code_subtotal = '70740050';
                    }
                }
                else if($orderCountryName == 'US')
                {
                  $code_subtotal = '70750010';
                }
                else if($orderCountryName == 'CH')
                {
                  $code_subtotal = '70750020';
                }
                else
                {
                    $code_subtotal = '70750000';
                }

                //rebate
                if($order->getMarketplacesPartnerCode() == 'rueducommerce' || $payment->getMethod() == 'rueducommerce')
                {
                     $code_rebate = '70971010';
                }
                else if($order->getMarketplacesPartnerCode() == 'fbd' || $payment->getMethod() == 'fbd')
                {
                     $code_rebate = '70971030';
                }
                else if($order->getFromSite() == 'cdiscount')
                {
                    $code_rebate = '70971040';
                }
                else if($payment->getMethod() == 'priceminister')
                {
                    $code_rebate = '70971050';
                }
                else if($amazone == '1')
			    {
			        if($orderCountryName == 'FR')
                    {
                       $code_rebate = '70971021';
                    }
                    else if($orderCountryName == 'DE')
                    {
                       $code_rebate = '70974031';
                    }
                    else if($orderCountryName == 'IT')
                    {
                       $code_rebate = '70974041';
                    }
                    else if($orderCountryName == 'ES')
                    {
                       $code_rebate = '70974051';
                    }
                    else if($orderCountryName == 'GB')
                    {
                       $code_rebate = '70974021';
                    }
                    else if($orderCountryName == 'US')
                    {
                       $code_rebate = '70975011';
                    }
                    else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                    {
                       $code_rebate = '70974090';
                    }
                    else
                    {
                        $code_rebate = '70975090';
                    }

                }
                else if($ebay == '1')
			    {
			        if($orderCountryName == 'FR')
                    {
                       $code_rebate = '70971022';
                    }
                    else if($orderCountryName == 'DE')
                    {
                       $code_rebate = '70974032';
                    }
                    else if($orderCountryName == 'IT')
                    {
                       $code_rebate = '70974042';
                    }
                    else if($orderCountryName == 'ES')
                    {
                       $code_rebate = '70974052';
                    }
                    else if($orderCountryName == 'GB')
                    {
                       $code_rebate = '70974022';
                    }
                    else if($orderCountryName == 'US')
                    {
                       $code_rebate = '70975012';
                    }
                    else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                    {
                       $code_rebate = '70974095';
                    }
                    else
                    {
                        $code_rebate = '70975095';
                    }
                }
                else if($orderCountryName == 'FR' || $orderCountryName == 'MC' || $orderCountryName == 'AD')
                {
                    if(number_format($tvaItem ['pTva'],2) == '20')
                    {
                        $code_rebate = '70971000';
                    }
                    else if(number_format($tvaItem ['pTva'],2) == '5.5')
                    {
                        $code_rebate = '70971500';
                    }
                }
                else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                {
                    if(number_format($tvaItem ['pTva'],2) == '0.0000')
                    {
                        $code_rebate = '70977000';
                    }
                    else
                    {
                       $code_rebate = '70974000';
                    }

                }
                else if($orderCountryName == 'GB')
                {
                    $code_rebate = '70974020';
                }
                else if($orderCountryName == 'DE')
                {
                    $code_rebate = '70974030';
                }
                else if($orderCountryName == 'IT')
                {
                    $code_rebate = '70974040';
                }
                else if($orderCountryName == 'ES')
                {
                    $code_rebate = '70974050';
                }
                else if($orderCountryName == 'US')
                {
                  $code_rebate = '70975010';
                }
                else if($orderCountryName == 'CH')
                {
                  $code_rebate = '70975020';
                }
                else
                {
                    $code_rebate = '70975000';
                }


                //shipping cost
                if($order->getMarketplacesPartnerCode() == 'rueducommerce' || $payment->getMethod() == 'rueducommerce')
                {
                     $code_shipping = '70850010';
                }
                else if($order->getMarketplacesPartnerCode() == 'fbd' || $payment->getMethod() == 'fbd')
                {
                     $code_shipping = '70850030';
                }
                else if($order->getFromSite() == 'cdiscount')
                {
                    $code_shipping = '70851040';
                }
                else if($payment->getMethod() == 'priceminister')
                {
                    $code_shipping = '70851050';
                }
                else if($amazone == '1')
			    {
			        if($orderCountryName == 'FR')
                    {
                       $code_shipping = '70851021';
                    }
                    else if($orderCountryName == 'DE')
                    {
                       $code_shipping = '70854031';
                    }
                    else if($orderCountryName == 'IT')
                    {
                       $code_shipping = '70854041';
                    }
                    else if($orderCountryName == 'ES')
                    {
                       $code_shipping = '70854051';
                    }
                    else if($orderCountryName == 'GB')
                    {
                       $code_shipping = '70854021';
                    }
                    else if($orderCountryName == 'US')
                    {
                       $code_shipping = '70855011';
                    }
                    else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                    {
                       $code_shipping = '70854090';
                    }
                    else
                    {
                        $code_shipping = '70855090';
                    }

                }
                else if($ebay == '1')
			    {
			        if($orderCountryName == 'FR')
                    {
                       $code_shipping = '70851022';
                    }
                    else if($orderCountryName == 'DE')
                    {
                       $code_shipping = '70854032';
                    }
                    else if($orderCountryName == 'IT')
                    {
                       $code_shipping = '70854042';
                    }
                    else if($orderCountryName == 'ES')
                    {
                       $code_shipping = '70854052';
                    }
                    else if($orderCountryName == 'GB')
                    {
                       $code_shipping = '70854022';
                    }
                    else if($orderCountryName == 'US')
                    {
                       $code_shipping = '70855012';
                    }
                    else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                    {
                       $code_shipping = '70854095';
                    }
                    else
                    {
                        $code_shipping = '70855095';
                    }
                }
                else if($orderCountryName == 'FR' || $orderCountryName == 'MC' || $orderCountryName == 'AD')
                {
                    $code_shipping = '70850000';
                }
                else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                {
                    if(number_format($tvaItem ['pTva'],2) == '0.00')
                    {
                        $code_shipping = '70857000';
                    }
                    else
                    {
                        $code_shipping = '70854000';
                    }
                }
                else if($orderCountryName == 'GB')
                {
                    $code_shipping = '70854020';
                }
                else if($orderCountryName == 'DE')
                {
                   $code_shipping = '70854030';
                }
                else if($orderCountryName == 'IT')
                {
                   $code_shipping = '70854040';
                }
                else if($orderCountryName == 'ES')
                {
                   $code_shipping = '70854050';
                }
                else if($orderCountryName == 'CH')
                {
                   $code_shipping = '70855020';
                }
                else if($orderCountryName == 'US')
                {
                   $code_shipping = '70855010';
                }
                else
                {
                   $code_shipping = '70855000';
                }


                //Vat Code
                if($orderCountryName == 'GB')
                {
                    $code_vat = '44571020';
                }
                else if($orderCountryName == 'DE')
                {
                    $code_vat = '44571030';
                }
                else if($orderCountryName == 'IT')
                {
                    $code_vat = '44571040';
                }
                else if($orderCountryName == 'ES')
                {
                    $code_vat = '44571050';
                }
                else
                {
                    if(number_format($tvaItem ['pTva'],2) == '20')
                    {
                        $code_vat = '44571999';
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
                }

                //DEE Code
                if($order->getMarketplacesPartnerCode() == 'rueducommerce' || $payment->getMethod() == 'rueducommerce')
                {
                     $code_dee = '70791010';
                }
                else if($order->getMarketplacesPartnerCode() == 'fbd' || $payment->getMethod() == 'fbd')
                {
                     $code_dee = '70791030';
                }
                else if($order->getFromSite() == 'cdiscount')
                {
                    $code_dee = '70791040';
                }
                else if($payment->getMethod() == 'priceminister')
                {
                    $code_dee = '70791050';
                }
                else if($amazone == '1')
			    {
			        if($orderCountryName == 'FR')
                    {
                       $code_dee = '70791021';
                    }
                    else if($orderCountryName == 'DE')
                    {
                       $code_dee = '70794031';
                    }
                    else if($orderCountryName == 'IT')
                    {
                       $code_dee = '70794041';
                    }
                    else if($orderCountryName == 'ES')
                    {
                       $code_dee = '70794051';
                    }
                    else if($orderCountryName == 'GB')
                    {
                       $code_dee = '70794021';
                    }
                    else if($orderCountryName == 'US')
                    {
                       $code_dee = '70795011';
                    }
                    else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                    {
                       $code_dee = '70794090';
                    }
                    else
                    {
                        $code_dee = '70795090';
                    }

                }
                else if($ebay == '1')
			    {
			        if($orderCountryName == 'FR')
                    {
                       $code_dee = '70791022';
                    }
                    else if($orderCountryName == 'DE')
                    {
                       $code_dee = '70794032';
                    }
                    else if($orderCountryName == 'IT')
                    {
                       $code_dee = '70794042';
                    }
                    else if($orderCountryName == 'ES')
                    {
                       $code_dee = '70794052';
                    }
                    else if($orderCountryName == 'GB')
                    {
                       $code_dee = '70794022';
                    }
                    else if($orderCountryName == 'US')
                    {
                       $code_dee = '70795012';
                    }
                    else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                    {
                       $code_dee = '70794095';
                    }
                    else
                    {
                        $code_dee = '70795095';
                    }
                }
                else if($orderCountryName == 'FR' || $orderCountryName == 'MC' || $orderCountryName == 'AD')
                {
                    if(number_format($tvaItem ['pTva'],2) == '20')
                    {
                        $code_dee = '70791000';
                    }
                    else if(number_format($tvaItem ['pTva'],2) == '5.5')
                    {
                        $code_dee = '70791500';
                    }
                }
                else if($orderCountryName == 'AT' || $orderCountryName == 'BE' || $orderCountryName == 'BG' || $orderCountryName == 'CY' ||
                $orderCountryName == 'DK' || $orderCountryName == 'EE' || $orderCountryName == 'FI' || $orderCountryName == 'GR' ||
                $orderCountryName == 'HU' || $orderCountryName == 'IE' || $orderCountryName == 'LV' || $orderCountryName == 'LT' ||
                $orderCountryName == 'LU' || $orderCountryName == 'MT' || $orderCountryName == 'NL' || $orderCountryName == 'PL' || $orderCountryName == 'PT' ||
                $orderCountryName == 'RO' || $orderCountryName == 'SK' || $orderCountryName == 'SI' || $orderCountryName == 'SE' ||
                $orderCountryName == 'CZ' || $orderCountryName == 'HR')
                {
                    if(number_format($tvaItem ['pTva'],2) == '0.0000')
                    {
                        $code_dee = '70797000';
                    }
                    else
                    {
                       $code_dee = '70794000';
                    }

                }
                else if($orderCountryName == 'GB')
                {
                    $code_dee = '70794020';
                }
                else if($orderCountryName == 'DE')
                {
                    $code_dee = '70794030';
                }
                else if($orderCountryName == 'IT')
                {
                    $code_dee = '70794040';
                }
                else if($orderCountryName == 'ES')
                {
                    $code_dee = '70794050';
                }
                else if($orderCountryName == 'US')
                {
                  $code_dee = '70795010';
                }
                else if($orderCountryName == 'CH')
                {
                  $code_dee = '70795020';
                }
                else
                {
                    $code_dee = '70795000';
                }

                //print_r($tvaItem);
                if($tvaItem['sstotht']!='0.0000')
                {
                    //echo "1";
                    $value =  '"'.$creditmemoDate .'","'.$orderSotreName.'","VE","0","'.$code_subtotal.'","'.$customer_name.'","'.$orderCountryName.'","","'.number_format($tvaItem['sstotht'],2,'.','').'","'.$from.'","'.$currency_data.'","'.$creditmemo->getIncrementId().'","'.$order_inc_id.'"';
                    @fwrite($fp, $value."\n");
                }

                if($tvaItem['port']!='0.0000')
                {
                     //echo "2";
                    $value =  '"'.$creditmemoDate .'","'.$orderSotreName.'","VE","0","'.$code_shipping.'","'.$customer_name.'","'.$orderCountryName.'","","'.number_format($tvaItem['port'],2,'.','').'","'.$from.'","'.$currency_data.'","'.$creditmemo->getIncrementId().'","'.$order_inc_id.'"';
                    @fwrite($fp, $value."\n");
                }

                if($tvaItem['tva']!='0.0000' || $tvaShipping > 0)
                {
                     //echo "3";
                     $Creditmemo_amount = $creditmemo->getTaxAmount();
                    if(number_format($total_baseamount_check,2) == number_format($total_check,2))
                    {
                        if($Creditmemo_amount == $tvaItem['tva'])
                            $Creditmemo_amount = $Creditmemo_amount - 0.01;
                    }
                    $value =  '"'.$creditmemoDate .'","'.$orderSotreName.'","VE","0","'.$code_vat.'","'.$customer_name.'","'.$orderCountryName.'","","'.number_format($Creditmemo_amount,2,'.','').'","'.$from.'","'.$currency_data.'","'.$creditmemo->getIncrementId().'","'.$order_inc_id.'"';
                    @fwrite($fp, $value."\n");
                }

                if($tvaItem['dee'] == 0)
                {
                     //echo "4";
                    unset($tvaItem['dee']);
                }
                else
                {
                     //echo "5";
                    $value =  '"'.$creditmemoDate .'","'.$orderSotreName.'","VE","0","'.$code_dee.'","'.$customer_name.'","'.$orderCountryName.'","","'.number_format($tvaItem['dee'],2,'.','').'","'.$from.'","'.$currency_data.'","'.$creditmemo->getIncrementId().'","'.$order_inc_id.'"';
                    @fwrite($fp, $value."\n");
                }

                if($tvaItem['rem']!='0.0000')
                {
                    //echo "6";
                    $value =  '"'.$creditmemoDate .'","'.$orderSotreName.'","VE","0","'.$code_rebate.'","'.$customer_name.'","'.$orderCountryName.'","'.number_format($tvaItem['rem'],2,'.','').'","","'.$from.'","'.$currency_data.'","'.$creditmemo->getIncrementId().'","'.$order_inc_id.'"';
                    @fwrite($fp, $value."\n");
                }
               /* echo "<pre>";
                print_r($value);*/
            }
        }
        //exit;
        fclose($fp);
        $this->_prepareDownloadResponse($file, file_get_contents(Mage::getBaseDir('export').'/'.$file));
	}
}