<?php
class MondialRelay_Pointsrelais_Model_Carrier_Pointsrelaislds extends Mage_Shipping_Model_Carrier_Abstract
{
	protected $_code = 'pointsrelaislds';

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
        $result = Mage::getModel('shipping/rate_result');
        if (!$this->getConfigData('active')) {
            return $result;
        }
        
        $shipping_free_cart_price = null;
        if ($this->getConfigData('free_active')) {
            	$shipping_free_cart_price = $this->getConfigData('free_price');
        }
		

        $request->setConditionName($this->getConfigData('condition_name') ? $this->getConfigData('condition_name') : $this->_default_condition_name);

        $result = Mage::getModel('shipping/rate_result');
        
        $rates = $this->getRate($request);
		$cartTmp = $request->_data['package_value_with_discount'];
		$weghtTmp = $request->_data['package_weight'];
        
        foreach($rates as $rate)
        {
            if (!empty($rate) && $rate['price'] >= 0) 
            {
				$method = Mage::getModel('shipping/rate_result_method');
				$method->setCarrier('pointsrelaislds');
				$method->setCarrierTitle($this->getConfigData('title'));
				$method->setMethod('pointsrelaislds');
				$method->setMethodTitle($this->getConfigData('description'));
				if($shipping_free_cart_price != null && ($cartTmp > $shipping_free_cart_price && $weghtTmp > 0.101)){
							$price = $rate['price'] = 0;
							$rate['cost']  = 0;
							$method->setPrice($price);
							$method->setCost($rate['cost']);
				}else{
					   		$price = $rate['price'];
						   	$method->setPrice($this->getFinalPriceWithHandlingFee($price));
							$method->setCost($rate['cost']);
				}
				$result->append($method);
            }            
        }

        return $result;
	}
		
	public function getRate(Mage_Shipping_Model_Rate_Request $request)
	{
		return Mage::getResourceModel('pointsrelais/carrier_pointsrelaislds')->getRate($request);
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

}