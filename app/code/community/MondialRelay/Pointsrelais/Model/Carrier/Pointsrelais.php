<?php
class MondialRelay_Pointsrelais_Model_Carrier_Pointsrelais extends Mage_Shipping_Model_Carrier_Abstract
{
	protected $_code = 'pointsrelais';
    protected $_picture = 'images/tatva/logo_mondial_relay.png';
	protected $_littlepicture = 'images/tatva/logo_little_mondial_relay.png';

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{


        $result = Mage::getModel('shipping/rate_result');
        $method = Mage::getModel('shipping/rate_result_method');
    	$method->setCarrier($this->_code);
		$method->setCarrierTitle($this->getConfigData('title'));

		$method->setMethod($this->_code);
		$method->setMethodTitle($this->getConfigData('title'));

		$method->setPicture($this->getPicture());
        if (!$this->getConfigData('active')) {
            return $result;
        }
        $country_id = $request->getDestCountryId();


        $request->setConditionName($this->getConfigData('condition_name') ? $this->getConfigData('condition_name') : $this->_default_condition_name);

        if($this->getConfigData('package_weight')){
        	$request->_data['package_weight'] = $request->_data['package_weight']+($this->getConfigData('package_weight')/1000);
        }



	    $cartTmp = $request->_data['package_value_with_discount'];
		$weghtTmp = $request->_data['package_weight'];
        $discountamount = 0;
		$price  = 0;
        $shipping_free_cart_price = null;
        if ($this->getConfigData('free_active')) {
               //$shipping_free_cart_price = $this->getConfigData('free_price');
              $shipping_free_cart_price = $this->getFreeShippingSubtotal($country_id);

              if($request->getCartTotal() >= $shipping_free_cart_price)
              {
                 $price  = 0;
                 $rates = $this->getRate($request);
                 foreach($rates as $rate)
                  {
                    if (!empty($rate) && $rate['price'] >= 0)
                      {
                        $discountamount = $rate['price'];
                      }
                  }
              }
              else
              {
                $rates = $this->getRate($request);
                 foreach($rates as $rate)
                  {
                    if (!empty($rate) && $rate['price'] >= 0)
                      {
                        $price = $rate['price'];
                      }
                  }
              }
        }
        else
        {
          $rates = $this->getRate($request);
              foreach($rates as $rate)
              {
                if (!empty($rate) && $rate['price'] >= 0)
                  {
                    $price = $rate['price'];
                  }
              }
        }


        $method->setPrice($price);
        $method->setAmountBeforeDiscount($discountamount);
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
     * Retourne la description courte
     *
     * @return string
     */
    public function getDescription(){//echo 'db--'.$this->getConfigData('description');
    	return $this->getConfigData('description');
    }

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

	public function getRate(Mage_Shipping_Model_Rate_Request $request)
	{        //echo '<pre>';print_r($request->getData());exit;
		return Mage::getResourceModel('pointsrelais/carrier_pointsrelais')->getRate($request);
	}

	public function getCode($type, $code='')
    {
        $codes = array(

            'condition_name'=>array(
                'package_weight' => Mage::helper('shipping')->__('Weight vs. Destination'),
                'package_value'  => Mage::helper('shipping')->__('Price vs. Destination'),
                'package_qty'    => Mage::helper('shipping')->__('# of Items vs. Destination'),
            ),

            'condition_name_short'=>array(
                'package_weight' => Mage::helper('shipping')->__('Poids'),
                'package_value'  => Mage::helper('shipping')->__('Valeur du panier'),
                'package_qty'    => Mage::helper('shipping')->__('Nombre d\'articles'),
            ),

        );

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Tablerate Rate code type: %s', $type));
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('shipping')->__('Invalid Tablerate Rate code for type %s: %s', $type, $code));
        }

        return $codes[$type][$code];
    }

    public function getAllowedMethods()
    {
        return array('pointsrelais'=>$this->getConfigData('name'));
    }

	public function isTrackingAvailable()
	{
		return true;
	}
	
	public function getTrackingInfo($tracking_number)
	{
		$tracking_result = $this->getTracking($tracking_number);

		if ($tracking_result instanceof Mage_Shipping_Model_Tracking_Result)
		{
			if ($trackings = $tracking_result->getAllTrackings())
			{
				return $trackings[0];
			}
		}
		elseif (is_string($tracking_result) && !empty($tracking_result))
		{
			return $tracking_result;
		}
		
		return false;
	}
	
	protected function getTracking($tracking_number)
	{
		$key = '<' . $this->getConfigData('marque_url') .'>' . $tracking_number . '<' . $this->getConfigData('cle_url') . '>';
		$key = md5($key);
		
		$tracking_url = 'http://www.mondialrelay.fr/lg_fr/espaces/url/popup_exp_details.aspx?cmrq=' . strtoupper($this->getConfigData('marque_url')) .'&nexp=' . strtoupper($tracking_number) . '&crc=' . strtoupper($key) ;

		$tracking_result = Mage::getModel('shipping/tracking_result');

		$tracking_status = Mage::getModel('shipping/tracking_result_status');
		$tracking_status->setCarrier($this->_code)
						->setCarrierTitle($this->getConfigData('title'))
						->setTracking($tracking_number)
						->setUrl($tracking_url);
		$tracking_result->append($tracking_status);

		return $tracking_result;
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

     public function getEtiquetteUrl($shipmentsIds)
    {
         Mage::Log('***getEtiquetteUrl****');
        //On récupère les infos d'expédition


        if (is_array($shipmentsIds))
        {
            foreach($shipmentsIds as $shipmentsId)
            {
                array_merge($this->_trackingNumbers, $this->getTrackingNumber($shipmentsId));
            }
            foreach($this->_trackingNumbers as $trackingId)
            {

                Mage::Log('********');
                Mage::Log('$trackingId : ',$trackingId);
                Mage::Log('********');
            }
        }
        else
        {
            $shipmentId = $shipmentsIds;
            $this->_trackingNumbers = $this->getTrackingNumber($shipmentId);
        };

        // On met en place les paramètres de la requète
        $params = array(
                       'Enseigne'       => $this->getConfigData('enseigne'),
                       'Expeditions'    => implode(';',$this->_trackingNumbers),
                       'Langue'    => 'FR',
        );

        //On crée le code de sécurité
        $code = implode("",$params);
        $code .= $this->getConfigData('cle');

        //On le rajoute aux paramètres
        $params["Security"] = strtoupper(md5($code));

        // On se connecte
        $client = new SoapClient("http://www.mondialrelay.fr/WebService/Web_Services.asmx?WSDL");

        // Et on effectue la requète
        $etiquette = $client->WSI3_GetEtiquettes($params)->WSI3_GetEtiquettesResult;

        return $etiquette->URL_PDF_10x15;
    }

    public function getTrackingNumber($shipmentId)
    {
                Mage::Log('***getTrackingNumber****');
Mage::Log('***getTrackingNumber**** 1 : '.$shipmentId);
        $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
        $trackingNumbersToReturn = array();
        //On récupère le numéro de tracking
        $tracks = $shipment->getTracksCollection();
        //->addAttributeToFilter('carrier_code', array('like' => 'pointsrelais%'));

        foreach ($tracks as $track) {
Mage::Log('***getTrackingNumber**** 2 : '.$track->getnumber());

                $trackingNumbersToReturn[] = $track->getnumber();

        }

        return $trackingNumbersToReturn;
    }

}