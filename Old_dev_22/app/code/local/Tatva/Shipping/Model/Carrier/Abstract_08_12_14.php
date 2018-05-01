<?php

/**
 * 
 * @package Tatva_Shipping
 */
abstract class Tatva_Shipping_Model_Carrier_Abstract
	extends Mage_Shipping_Model_Carrier_Abstract
{
  	protected $_formBlockType = 'tatvashipping/carrier_form';
	protected $_infoBlockType = 'tatvashipping/carrier_info';
	protected $_picture = "";
	protected $_littlepicture = "";
	protected $_amountBeforeDiscount;
	protected $_cmsBlockPopup = "";
	
	abstract public function getDiscountAmount($request);	

	/**
     * Calcul des frais de port
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {

    	$result = Mage::getModel('shipping/rate_result');
    	$method = Mage::getModel('shipping/rate_result_method');
    	$method->setCarrier($this->_code);
		$method->setCarrierTitle($this->getConfigData('title'));

		$method->setMethod($this->_code);
		$method->setMethodTitle($this->getConfigData('name'));

		$method->setPicture($this->getPicture());

		$price = 0;
        $country_id = $request->getDestCountryId();

		//Si utilisation d'un coupon comportant les frais de port gratuit
        if (!$request->getFreeShipping() ){

			//REG PC-422
	        //Frais de port gratuit si le montant de la commande dépasse le seuil
	        if($this->getConfigData('free_shipping_enable') == true){
				$seuil = $this->getFreeShippingSubtotal($country_id);
				//getPackageValueWithDiscount replace with getPackageValue
				//if($seuil && ( $request->getPackageValue() >= $seuil )) {
				if($seuil && ( $request->getCartTotal() >= $seuil )){	            
	            	$price = 0;
					$this->collectShippingAmount($request,$country_id,$request->getPackageWeight());
	            }else{
	            	$price = $this->collectShippingAmount($request,$country_id,$request->getPackageWeight());
	            }
			}else{
				$price = $this->collectShippingAmount($request,$country_id,$request->getPackageWeight());
			}
    	}

		$method->setPrice($price);

		$method->setAmountBeforeDiscount($this->_amountBeforeDiscount);
		$method->setCost($price);

		$result->append($method);
    	return $result;
    }
    
    /**
     * Récupére le seuil du montant pour les frais de port gratuit
     * @param $request
     * @param string $countryId
     * @return double
     */
    public function getFreeShippingSubtotal($countryId){
    	$freeShippingSubtotal = unserialize($this->getConfigData('free_shipping_subtotal'));
    	$found = false;
    	$result = false;

    	if(!empty($freeShippingSubtotal) && is_array($freeShippingSubtotal)){
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
    	}	
		
    	return $result;
    }
    
   /**
     * Calcul les frais de port 
     * @param $request
     * @param string $countryId
     * @param double $weight
     * @return double
     */
    public function collectShippingAmount($request, $countryId,$weight){
    $code = $this->getCode();
    if($this->getCode() == 'colissimoappointment' || $this->getCode() == 'colissimolocalstore' || $this->getCode() == 'colissimopostoffice' || $this->getCode() == 'colissimocityssimo')
    {
        $code = 'colissimo';
    }

    	$collection = Mage::getModel('tatvashipping/rule')
    		->getCollection()
    		->addShippingFilter($code)
    		->addCountryFilter($countryId)
    		->addWeightFilter($weight);
    	$rule = $collection->getFirstItem();
        //echo $collection->getSelect();  exit;
    	if($rule && $rule->getId()){
    	    $amount = $rule->getAmount();
    		$this->_amountBeforeDiscount = $amount;
			
    		$discount = $this->getDiscountAmount($request);
    		//REG PC-425
		   
    		$price = $amount - $discount;

            
                $special_fee = Mage::getStoreConfig('carriers/'.$this->getCode().'/specialfee');
                if($special_fee != "" && $special_fee != 0);
                {
                    $price = $price + $special_fee;
                }

            

    		if($price > 0){
    			return $price;
    		}
    		return 0;
    	}
    	return false;
    }
    
    /**
     * Retrieves the delivery times
     * @param countryCode string
     * @return string
     */
    public function getDeliveryTimes($countryCode,$path)
    {  
	    $deliveryTimes = unserialize(Mage::getStoreConfig($path));
     	$result = "";
     	$found = false;
     	if(!empty($deliveryTimes) && is_array($deliveryTimes))
        {
        	foreach($deliveryTimes as $lines)
            {
        		if(!empty($lines['countries']))
                {
        			foreach($lines['countries'] as $country)
                    {
        				if($countryCode == $country)
                        {
        					$result = $lines['value'];
        					$found = true;
        					break;
        				}
        			}
        		}
        		if($found)
                {
        			break;
        		}
        	}
     	}
     	return $result;

	}
    
    /**
     * Retrieves the delivery days
     * @param countryCode string
     * @return int
     */
    public function getDeliveryDays($countryCode){ 
    	$deliveryTimes = unserialize($this->getConfigData('delivery_text'));
		
		$result = 0;
    	$found = false;
    	if(!empty($deliveryTimes) && is_array($deliveryTimes)){
	    	foreach($deliveryTimes as $lines){
	    		if(!empty($lines['countries'])){
	    			foreach($lines['countries'] as $country){
	    				if($countryCode == $country){
	    					$result = $lines['days'];
	    					$found = true;
	    					break;
	    				}
	    			}
	    		}
	    		if($found){
	    			break;
	    		}
	    	}    
    	}	
    	return $result;
    }
    
    /**
     * Retrieve carrier's code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }
    
    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getFormBlockType()
    {
        return $this->_formBlockType;
    }
    
    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getInfoBlockType()
    {
        return $this->_infoBlockType;
    }
    
    /**
     * Retrieves the picture's path
     * 
     * @return string
     */
    public function getPicture(){
    	return $this->_picture;
    }
    
    /**
     * Retrieves the little picture's path
     * 
     * @return string
     */
    public function getLittlePicture(){
    	return $this->_littlepicture;
    }
    
    /**
     * Retrieves the cms block
     * 
     * @return string
     */
    public function getCmsBlockPopup(){
    	return $this->_cmsBlockPopup;
    }
    
    /**
     * Retourne la description courte
     * 
     * @return string
     */
    public function getDescription(){//echo 'db--'.$this->getConfigData('description');
    	return $this->getConfigData('description');
    }
    
    public function getTitle($store){
    	if (empty($this->_code)) {
            return false;
        }
        $path = 'carriers/'.$this->_code.'/title';
        return Mage::getStoreConfig($path, $store);
    }
    
}
