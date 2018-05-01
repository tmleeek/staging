<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Model_Carrier_Colissimolocalstore
    extends Tatva_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'colissimolocalstore';
	protected $_picture = 'images/tatva/logo_colissimolocalstore.png';
	protected $_littlepicture = 'images/tatva/logo_little_colissimolocalstore.png';
	protected $_cmsBlockPopup = 'popup_livraison_colissimo';

    public function isTrackingAvailable()
    {
        return true;
    }
    
    public function getAllowedMethods()
    {
        return array('colissimolocalstore'=>$this->getConfigData('name'));
    }
    
    public function getDiscountAmount($request){
    	
    	
       
	    $country_id = $request->getDestCountryId();
        if($request->getDestRegionId() != '' || $request->getDestRegionId() != 0)
        {
            $regionModel = Mage::getModel('directory/region')->load($request->getDestRegionId());

            if($regionModel->getCode() == 'CE')
			{
			    $country_id = $regionModel->getCode();	
			}
            else if($regionModel->getCode() == 'GP')
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
                $postcode = substr($request->getDestPostcode(),2,3);
                if( ($postcode >= 365 && $postcode <= 428) || ($postcode >= 500 && $postcode <= 656) || ($postcode >= 750 && $postcode <= 782) || ($postcode >= 790 && $postcode <= 796) || ($postcode >= 800 && $postcode <= 899) )
                {
                    $country_id = $regionModel->getCode();
                }
            }

        }
	
	
	    $gls = Mage::getModel('tatvashipping/carrier_gls');
		$var_gls = Mage::getStoreConfig('carriers/gls/specificcountry');
		$var_gls_arr = explode(',',$var_gls);
	    if (in_array($country_id, $var_gls_arr)) 
		{
		    //echo 'yes--'.$var_gls;

			
		   
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
