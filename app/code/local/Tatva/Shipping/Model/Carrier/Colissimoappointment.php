<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Carrier_Colissimoappointment
    extends Tatva_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'colissimoappointment';
	protected $_picture = 'images/tatva/logo_colissimoappointment.png';
	protected $_littlepicture = 'images/tatva/logo_little_colissimoappointment.png';
	protected $_cmsBlockPopup = 'popup_livraison_colissimo';

    public function isTrackingAvailable()
    {
        return true;
    }
    
    public function getAllowedMethods()
    {
        return array('colissimoappointment'=>$this->getConfigData('name'));
    }
    
    public function getDiscountAmount($request){
    	return 0;
    }

    public function getTrackingUrl($number){
    	$url = $this->getConfigData('url_tracking');
    	return $url.'&colispart=' . $number;
    }
    
}
