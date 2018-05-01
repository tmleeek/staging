<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Carrier_Colissimo
    extends Tatva_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'colissimo';
    protected $_picture = 'images/tatva/logo_colissimo.png';
	protected $_littlepicture = 'images/tatva/logo_little_colissimo.png';
	protected $_cmsBlockPopup = 'popup_livraison_colissimo';
	
    public function isTrackingAvailable()
    {
        return true;
    }

    public function getAllowedMethods()
    {
        return array('colissimo'=>$this->getConfigData('name'));
    }

    public function getDiscountAmount($request){


	    $country_id = $request->getDestCountryId();
        if($request->getDestRegionId() != '' || $request->getDestRegionId() != 0)
        {
            $regionModel = Mage::getModel('directory/region')->load($request->getDestRegionId());

            $regionModel_code = $regionModel->getCode();

            $special_region_code = array("CE", "GP", "MQ", "GF","RE", "PM", "YT", "TF","WF", "PF", "NC", "MC", "MF", "BL");

            if (in_array($regionModel_code, $special_region_code) && $country_id == "FR")
            {
               $country_id = $regionModel_code;
            }
            else if($regionModel_code == 'YY')
            {
                $postcode = substr($request->getDestPostcode(),2,3);
                if( ($postcode >= 365 && $postcode <= 428) || ($postcode >= 500 && $postcode <= 656) || ($postcode >= 750 && $postcode <= 782) || ($postcode >= 790 && $postcode <= 796) || ($postcode >= 800 && $postcode <= 899) )
                {
                    $country_id = $regionModel->getCode();
                }
            }

        }
		//if(($country_id == 'FR' && $regionModel->getCode() != 'CE')  ||  $country_id == 'MC')
	    $gls = Mage::getModel('tatvashipping/carrier_gls');
		$var_gls = Mage::getStoreConfig('carriers/gls/specificcountry');
		
		$var_gls_arr = explode(',',$var_gls);
	    if (in_array($country_id, $var_gls_arr)) 
		{
			if($gls->getConfigData('free_shipping_enable') == true && $this->getConfigData('deduct_colissimo_amount') == 1 ){
				$seuil = $gls->getFreeShippingSubtotal($country_id); //echo $request->getCartTotal() .'>='. $seuil;
		        if($seuil && $request->getCartTotal() >= $seuil){    
				
		        	return $gls->collectShippingAmount($request,$country_id,$request->getPackageWeight());
		        }
			}
		}
		return false;
    }

    public function getTrackingUrl($number){
    	$url = $this->getConfigData('url_tracking');
    	return $url.'&colispart=' . $number;
    }
    
}
