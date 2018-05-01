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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wishlist sidebar block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tatva_Checkout_Block_Cart_Sidebar extends Mage_Checkout_Block_Cart_Sidebar
{
    public function remainingshippingamount()
	{
	   $freeShipping = false;

        $var_free = 0;
        $var_mondial = 0;
        $freeShippingEnable = Mage::getStoreConfig('carriers/gls/free_shipping_enable');
        $freeShippingMondialEnable = Mage::getStoreConfig('carriers/pointsrelais/free_active');
		
		if($freeShippingEnable == 1 || $freeShippingMondialEnable == 1)
        {    $countryId = false;
    		 if(sizeof($this->getQuote()->getAllAddresses()) > 0 ){
    		 	$shippingAddress = $this->getQuote()->getShippingAddress();

    		 	if($shippingAddress){
    		 	   $countryId = $shippingAddress->getCountry(); ;
    		 	}
    		 }

    		 if(!$countryId){

                $customerSession = Mage::getSingleton('customer/session');
                if($customerSession->isLoggedIn())
                {
                  $customer = Mage::getModel('customer/session')->getCustomer();
                  $address = $customer->getDefaultShippingAddress();
                  if(!$address)
                  {
                    $countryId = Mage::getStoreConfig('general/country/default');  
                  }
                  else
                  {
                     $countryId = $address->getCountryId();
                  }

                }
                else
                {
    		 	 $countryId = Mage::getStoreConfig('general/country/default');
                }
    		 }

                    $subtotal = $this->getSubtotal();

		    		foreach (Mage::getSingleton('checkout/cart')->getItems() as $item){
		    			if($item->getIsVirtual()){
		    				$subtotal -= $item->getRowTotal();
		    			}
		    		}

        }	
	
	/*return "Encore 66,61 € d'achats et la livraison en relais colis est offerte</span> Livraison gratuite en relais colis dès 95 € d'achats et à domicile dès 150 € d'achats";*/
	
	//for mondial
        if($freeShippingMondialEnable)
        {
            $freeshipmondial = $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("freeshipmondial")->toHtml());
            $freeshipmondial2 = $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("freeshipmondial2")->toHtml());
            $freeshipmetropolitan = $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("freeshipmetropolitan")->toHtml());
            $freevatblock = $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("freevatblock")->toHtml());
            $francemetropolitan = $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("francemetropolitan")->toHtml());
            $withgls = $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("withgls")->toHtml());
            $tofrancemetropolitan = $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("tofrancemetropolitan")->toHtml());
            $ukfreegls1 = $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("ukfreegls1")->toHtml());    
            $ukfreegls2 = $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("ukfreegls2")->toHtml());  
            $shipcmstext1= $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("cartshipping1")->toHtml());
            $shipcmstext2= $this->getstripdata($this->getLayout()->createBlock("cms/block")->setBlockId("cartshipping2")->toHtml());
            $freeShippingSubtotal = unserialize(Mage::getStoreConfig('carriers/pointsrelais/free_shipping_subtotal'));
            
            
                   if(!empty($freeShippingSubtotal) && is_array($freeShippingSubtotal))
                   {

                       $found = false;
            			$result = false;
            			foreach($freeShippingSubtotal as $lines){
        	    			if(!empty($lines['countries'])){
        		    			foreach($lines['countries'] as $country){
        		    				if($countryId == $country){
        		    					$result = $lines['value'];
        		    					$found = true;
        		    					break;
        		    				}
        		    			}
        		    		}
        		    		if($found){
        		    			break;
        		    		}
        		    	}
                       if($found)
                       {
                           if(!Mage::getStoreConfig('sales/totals_sort/subtotal_enabled')){
        			    		$classTax = Mage::getModel('tax/class')->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)->load('TVA normale','class_name');
        			    		$request = Mage::getSingleton('tax/calculation')->getRateRequest();
        			    		$request->setCountryId($countryId);
        			    		$request->setProductClassId($classTax->getId());
        			    		$tax = Mage::getSingleton('tax/calculation')->getRate($request);

        			    		$resTax = number_format($tax / 100 ,3,',','');
        			    		//$result = $result * (1 + ($tax / 100));
        			    		  //	$result = round ($result, 2);

        		    		}
                            $freeShipping .= '<strong>';
                            if($subtotal >= $result){
                                            
        		    			$freeShipping .= Mage::helper('tatvacheckout')->__($freeshipmondial).'<br>';
                                $var_free = 1;
        		    		}else{
        		    			$diff = $result - $subtotal;
                                $var_mondial = $diff;
        		    			$diff = $this->helper('checkout')->formatPrice($diff);
        		    			$freeShipping .= Mage::helper('tatvacheckout')->__('%s '.$freeshipmondial2."",'<span class="texte-rose">' . $diff . "</span>").'<br>';

                            }
                            $freeShipping .= '</strong>';
                            
                            $freeShipping .= '<span class="texte-noir display-block align-center x-small-text" style="padding-top:2px;">'.Mage::helper('tatvacheckout')->__($freeshipmetropolitan).'</span><br>';
                       }
                   }


        }
	
	//for collisimo

    	if($freeShippingEnable){
    		$freeShippingSubtotal = unserialize(Mage::getStoreConfig('carriers/gls/free_shipping_subtotal'));


    		if(!empty($freeShippingSubtotal) && is_array($freeShippingSubtotal)){
    			$found = false;
    			$result = false;
    			foreach($freeShippingSubtotal as $lines){
	    			if(!empty($lines['countries'])){
		    			foreach($lines['countries'] as $country){
		    				if($countryId == $country){
		    					//$result = $lines['value'];
                                                    if($countryId == "GB")
                                                    {
                                                        $result = 90;
                                                    }
                                                    else
                                                    {
		    					$result = $lines['value'];
                                                    }
		    					$found = true;
		    					break;
		    				}
		    			}
		    		}
		    		if($found){
		    			break;
		    		}
		    	}
		    	if($found){

                    if(!Mage::getStoreConfig('sales/totals_sort/subtotal_enabled')){
			    		$classTax = Mage::getModel('tax/class')->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)->load('TVA 20%','class_name');
			    		$request = Mage::getSingleton('tax/calculation')->getRateRequest();
			    		$request->setCountryId($countryId);
			    		$request->setProductClassId($classTax->getId());
			    		$tax = Mage::getSingleton('tax/calculation')->getRate($request);

			    		$resTax = number_format($tax / 100 ,3,',','');
			    		//$result = $result * (1 + ($tax / 100));
			    		//$result = round ($result, 2);

		    		}


		    		if($subtotal >= $result){

                        if($var_free == 1)

                        {
                            
                             $freeShipping = Mage::helper('tatvacheckout')->__($freevatblock);

                             $freeShipping .=  '<br><span class="texte-noir display-block align-center x-small-text" style="padding-top:2px;">'.Mage::helper('tatvacheckout')->__($francemetropolitan).'</span>'.Mage::helper('tatvacheckout')->__('or').' <br><strong>'.Mage::helper('tatvacheckout')->__($withgls).'</strong> <br><span class="texte-noir display-block align-center x-small-text" style="padding-top:2px;">'.Mage::helper('tatvacheckout')->__($tofrancemetropolitan).'</span>';
                        }
                        else
                        {
                        
                        $freeShipping .= '<strong>';
		    			$freeShipping .= Mage::helper('tatvacheckout')->__($ukfreegls1);
                        $freeShipping .= '</strong>';

                        $freeShipping .= '<span class="texte-noir display-block align-center x-small-text" style="padding-top:2px;">'.Mage::helper('tatvacheckout')->__($ukfreegls2).'</span>';
                        }
		    		}else{ 
                      //if mondial not amount greater than cart amount
                        if($var_mondial == 0)
                        {
		    			$diff = $result - $subtotal;
		    			$diff = $this->helper('checkout')->formatPrice($diff);
                        $freeShipping .= '<strong>';
                        
		    			$freeShipping .= Mage::helper('tatvacheckout')->__('%s '.$shipcmstext1."",'<span class="texte-rose">' . $diff . "</span>");
                        $freeShipping .= '</strong>';

                        $freeShipping .= '<span class="texte-noir display-block align-center x-small-text" style="padding-top:2px;">'.Mage::helper('tatvacheckout')->__($shipcmstext2).'</span>';
                       }

                    }


		    	}
    		}
    	}

    	return $freeShipping;
	}	
        public function getstripdata($string)
        {
            //$value = preg_replace('/<p\b[^>]*>(.*?)<\/p>/i', '', $string);

            
            //echo $value;die();
            return $string;
        }
}
