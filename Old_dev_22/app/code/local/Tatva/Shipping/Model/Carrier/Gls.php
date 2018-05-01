<?php
/**
 * created : 9 septembre 2009
 * GLS shipping model
 * @category SQLI
 * @package Sqli_Shipping
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 *
 * @package Sqli_Shipping
 */
class Tatva_Shipping_Model_Carrier_Gls
    extends Tatva_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'gls';
    
    protected $_picture = 'images/tatva/logo_little_gls.png';
    protected $_littlepicture = 'images/tatva/logo_little_gls.png';
	protected $_cmsBlockPopup = 'popup_livraison_gls';
	
    public function isTrackingAvailable()
    {
        return true;
    }
    
    public function getAllowedMethods()
    {
        return array('gls'=>$this->getConfigData('name'));
    }
    
    /**
     * Calcul la remise des frais port du colissimo quand celui ci est gratuit
     * REG PC-425
     * @param $request
     * @return double  getPackageValueWithDiscount
     */
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
       
    	$colissimo = Mage::getModel('tatvashipping/carrier_colissimo');
		if($colissimo->getConfigData('free_shipping_enable') == true && $this->getConfigData('deduct_colissimo_amount') == 1 ){
			$seuil = $colissimo->getFreeShippingSubtotal($country_id);

	        if($seuil && $request->getPackageValue() >= $seuil){
	        	return $colissimo->collectShippingAmount($request,$country_id,$request->getPackageWeight());
	        }
		}
		return false;
    }
    
    public function getTrackingUrl($number){
    	$url = $this->getConfigData('url_tracking');
    	return $url.'?listeNumeros=' . $number;
    }

}
