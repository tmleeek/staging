<?php

class Tatva_Usa_Model_Shipping_Carrier_Ups extends Mage_Usa_Model_Shipping_Carrier_Ups
{

   	protected $_formBlockType = 'tatvausa/carrier_form_ups';
	protected $_picture = 'images/logo_ups.png';
	protected $_littlepicture = 'images/logo_little_ups.png';

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
     * Retourne l'url de tracking colis
     * @param $number
     * @return unknown_type
     */
    public function getTrackingUrl($number){
    	return "http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&error_carried=true&tracknums_displayed=5&TypeOfInquiryNumber=T&loc=en_US&InquiryNumber1=$number&AgreeToTermsAndConditions=yes";
    }

    /**
     * Calcul la remise des frais port du colissimo quand celui ci est gratuit
     * REG PC-425
     * @param $request
     * @return double
     */
    public function getDiscountAmount(){
    	$request = $this->_request;
    	$country_id = $request->getDestCountryId();
        if($request->getDestRegionId() != '' || $request->getDestRegionId() != 0)
        {
            $regionModel = Mage::getModel('directory/region')->load($request->getDestRegionId());

            if($regionModel->getCode() == 'GP')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'MQ')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'GF')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'RE')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'PM')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'YT')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'TF')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'WF')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'PF')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'NC')
            {
                $country_id = $regionModel->getCode();
            }
            else if($regionModel->getCode() == 'MC')
            {
                $country_id = $regionModel->getCode();
            }
			else if($regionModel->getCode() == 'YY')
            {
                $country_id = $regionModel->getCode();
            }

        }
    	$colissimo = Mage::getModel('tatvashipping/carrier_colissimo');
		if($colissimo->getConfigData('free_shipping_enable') == true && $this->getConfigData('deduct_colissimo_amount') == 1){
			$seuil = $colissimo->getFreeShippingSubtotal($country_id);

	        if($seuil && $request->getPackageValueWithDiscount() >= $seuil){
	        	return $colissimo->collectShippingAmount($request,$country_id,$request->getPackageWeight());
	        }
		}
		return false;
    }

    protected function _parseCgiResponse($response)
    {
        $costArr = array();
        $priceArr = array();
        $errorTitle = Mage::helper('usa')->__('Unknown error');
        $discountAmount = $this->getDiscountAmount();

        if (strlen(trim($response))>0) {
            $rRows = explode("\n", $response);
            $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
            foreach ($rRows as $rRow) {
                $r = explode('%', $rRow);
                switch (substr($r[0],-1)) {
                    case 3: case 4:
                        if (in_array($r[1], $allowedMethods)) {
                            $responsePrice = Mage::app()->getLocale()->getNumber($r[8]);
                            $costArr[$r[1]] = $responsePrice;
                            $priceArr[$r[1]] = $this->getMethodPrice($responsePrice, $r[1]);
                        }
                        break;
                    case 5:
                        $errorTitle = $r[1];
                        break;
                    case 6:
                        if (in_array($r[3], $allowedMethods)) {
                            $responsePrice = Mage::app()->getLocale()->getNumber($r[10]);
                            $costArr[$r[3]] = $responsePrice;
                            $priceArr[$r[3]] = $this->getMethodPrice($responsePrice, $r[3]);
                        }
                        break;
                }
            }
            asort($priceArr);
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            //$error->setErrorMessage($errorTitle);
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('ups');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $method_arr = $this->getCode('method', $method);
                $rate->setMethodTitle(Mage::helper('usa')->__($method_arr));
                if($costArr[$method] > 0){
                	$new_cost = $costArr[$method] - $discountAmount;
                	if($new_cost > 0)
                		$rate->setCost($new_cost);
					else
						$rate->setCost(0);
                }else{
                	$rate->setCost($costArr[$method]);
                }

                if($price > 0){
                	$new_price = $price - $discountAmount;
                	if($new_price > 0)
                		$rate->setPrice($new_price);
					else
						$rate->setPrice(0);
                }else {
                	$rate->setPrice($price);
                }

                $rate->setAmountBeforeDiscount($costArr[$method]);
                $result->append($rate);
            }
        }
        return $result;
    }

    protected function _parseXmlResponse($xmlResponse)
    {
        $costArr = array();
        $priceArr = array();
        $discountAmount = $this->getDiscountAmount();
        if (strlen(trim($xmlResponse))>0) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadString($xmlResponse);
            $arr = $xml->getXpath("//RatingServiceSelectionResponse/Response/ResponseStatusCode/text()");
            $success = (int)$arr[0][0];
            if($success===1){
                $arr = $xml->getXpath("//RatingServiceSelectionResponse/RatedShipment");
                $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));

                // Negotiated rates
                $negotiatedArr = $xml->getXpath("//RatingServiceSelectionResponse/RatedShipment/NegotiatedRates");
                $negotiatedActive = $this->getConfigFlag('negotiated_active')
                    && $this->getConfigData('shipper_number')
                    && !empty($negotiatedArr);

                foreach ($arr as $shipElement){
                    $code = (string)$shipElement->Service->Code;
                    #$shipment = $this->getShipmentByCode($code);
                    if (in_array($code, $allowedMethods)) {

                        if ($negotiatedActive) {
                            $cost = $shipElement->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue;
                        } else {
                            $cost = $shipElement->TotalCharges->MonetaryValue;
                        }

                        $costArr[$code] = $cost;
                        $priceArr[$code] = $this->getMethodPrice(floatval($cost),$code);
                    }
                }
            } else {
                $arr = $xml->getXpath("//RatingServiceSelectionResponse/Response/Error/ErrorDescription/text()");
                $errorTitle = (string)$arr[0][0];
                $error = Mage::getModel('shipping/rate_result_error');
                $error->setCarrier('ups');
                $error->setCarrierTitle($this->getConfigData('title'));
                $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            }
        }

        $result = Mage::getModel('shipping/rate_result');
        $defaults = $this->getDefaults();
        if (empty($priceArr)) {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            if(!isset($errorTitle)){
                $errorTitle = Mage::helper('usa')->__('Cannot retrieve shipping rates');
            }
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        } else {
            foreach ($priceArr as $method=>$price) {
                $rate = Mage::getModel('shipping/rate_result_method');
                $rate->setCarrier('ups');

                $rate->setMethod($method);

            	//$method_arr = $this->getCode('method', $method);
                switch ($method){
              	 	case '07' : $method_arr = Mage::helper('usa')->__('UPS Express');break;
              	 	case '08' : $method_arr = Mage::helper('usa')->__('UPS Expedited');break;
              	 	case '11' : $method_arr = Mage::helper('usa')->__('UPS Standard');break;
              	 	case '54' : $method_arr = Mage::helper('usa')->__('UPS Worldwide Express PlusSM');break;
              	 	case '65' : $method_arr = Mage::helper('usa')->__('UPS Saver');break;
              	 	default : break;
                }
                $rate->setCarrierTitle($this->getConfigData('title') . ' - ' . $method_arr);
                $rate->setMethodTitle($method_arr);
                if($costArr[$method] > 0){
                	$new_cost = $costArr[$method] - $discountAmount;
                	if($new_cost > 0)
                		$rate->setCost($new_cost);
					else
						$rate->setCost(0);
                }else{
                	$rate->setCost($costArr[$method]);
                }

                if($price > 0){
                	$new_price = $price - $discountAmount;
                	if($new_price > 0)
                		$rate->setPrice($new_price);
					else
						$rate->setPrice(0);
                }else {
                	$rate->setPrice($price);
                }

                $rate->setAmountBeforeDiscount($costArr[$method]);
                $result->append($rate);
            }
        }
        return $result;
    }

    /**
     * Retrieves the delivery times
     * @param countryCode string
     * @return string
     */
    public function getDeliveryTimes($countryCode){
    	$deliveryTimes = unserialize($this->getConfigData('delivery_text'));
    	$result = "";
    	$found = false;
    	if(!empty($deliveryTimes) && is_array($deliveryTimes)){
	    	foreach($deliveryTimes as $lines){
	    		if(!empty($lines['countries'])){
	    			foreach($lines['countries'] as $country){
	    				if($countryCode == $country){
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

    public function setRequest(Mage_Shipping_Model_Rate_Request $request)
    {
        $this->_request = $request;

        $r = new Varien_Object();

        if ($request->getLimitMethod()) {
            $r->setAction($this->getCode('action', 'single'));
            $r->setProduct($request->getLimitMethod());
        } else {
            $r->setAction($this->getCode('action', 'all'));
            $r->setProduct('GND'.$this->getConfigData('dest_type'));
        }

        if ($request->getUpsPickup()) {
            $pickup = $request->getUpsPickup();
        } else {
            $pickup = $this->getConfigData('pickup');
        }
        $r->setPickup($this->getCode('pickup', $pickup));

        if ($request->getUpsContainer()) {
            $container = $request->getUpsContainer();
        } else {
            $container = $this->getConfigData('container');
        }
        $r->setContainer($this->getCode('container', $container));

        if ($request->getUpsDestType()) {
            $destType = $request->getUpsDestType();
        } else {
            $destType = $this->getConfigData('dest_type');
        }
        $r->setDestType($this->getCode('dest_type', $destType));

        if ($request->getOrigCountry()) {
            $origCountry = $request->getOrigCountry();
        } else {
            $origCountry = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());
        }

        $r->setOrigCountry(Mage::getModel('directory/country')->load($origCountry)->getIso2Code());

        if ($request->getOrigRegionCode()) {
            $origRegionCode = $request->getOrigRegionCode();
        } else {
            $origRegionCode = Mage::getStoreConfig('shipping/origin/region_id', $this->getStore());
            if (is_numeric($origRegionCode)) {
                $origRegionCode = Mage::getModel('directory/region')->load($origRegionCode)->getCode();
            }
        }
        $r->setOrigRegionCode($origRegionCode);

        if ($request->getOrigPostcode()) {
            $r->setOrigPostal($request->getOrigPostcode());
        } else {
            $r->setOrigPostal(Mage::getStoreConfig('shipping/origin/postcode', $this->getStore()));
        }

        if ($request->getOrigCity()) {
            $r->setOrigCity($request->getOrigCity());
        } else {
            $r->setOrigCity(Mage::getStoreConfig('shipping/origin/city', $this->getStore()));
        }


        if ($request->getDestCountryId()) {
           $destCountry = $request->getDestCountryId();

                if($request->getDestRegionId() != '' || $request->getDestRegionId() != 0)
                {
                    $regionModel = Mage::getModel('directory/region')->load($request->getDestRegionId());

                    if($regionModel->getCode() == 'GP')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'MQ')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'GF')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'RE')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'PM')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'YT')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'TF')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'WF')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'PF')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'NC')
                    {
                        $destCountry = $regionModel->getCode();
                    }
                    else if($regionModel->getCode() == 'MC')
                    {
                        $destCountry = $regionModel->getCode();
                    }
					else if($regionModel->getCode() == 'YY')
                    {
                        $postcode = substr($request->getDestPostcode(),2,3);
                        if( ($postcode >= 365 && $postcode <= 428) || ($postcode >= 500 && $postcode <= 656) || ($postcode >= 750 && $postcode <= 782) || ($postcode >= 790 && $postcode <= 796) || ($postcode >= 800 && $postcode <= 899) )
                        {
                            $country_id = $regionModel->getCode();
                        }
                    }

                }
        } else {
            $destCountry = self::USA_COUNTRY_ID;
        }

        //for UPS, puero rico state for US will assume as puerto rico country
        if ($destCountry==self::USA_COUNTRY_ID && ($request->getDestPostcode()=='00912' || $request->getDestRegionCode()==self::PUERTORICO_COUNTRY_ID)) {
            $destCountry = self::PUERTORICO_COUNTRY_ID;
        }

        $r->setDestCountry(Mage::getModel('directory/country')->load($destCountry)->getIso2Code());

        $r->setDestRegionCode($request->getDestRegionCode());

        if ($request->getDestPostcode()) {
            $r->setDestPostal($request->getDestPostcode());
        } else {

        }

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeight($weight);
        if ($request->getFreeMethodWeight()!=$request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        $r->setValue($request->getPackageValue());
        $r->setValueWithDiscount($request->getPackageValueWithDiscount());

        if ($request->getUpsUnitMeasure()) {
            $unit = $request->getUpsUnitMeasure();
        } else {
            $unit = $this->getConfigData('unit_of_measure');
        }
        $r->setUnitMeasure($unit);

        $this->_rawRequest = $r;

        return $this;
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



}
