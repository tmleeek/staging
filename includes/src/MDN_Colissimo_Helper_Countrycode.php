<?php
class MDN_Colissimo_Helper_Countrycode extends Mage_Core_Helper_Abstract
{
    public function getCountryCode($country_name){

        $countryList = Mage::getResourceModel('directory/country_collection')
            ->loadData()
            ->toOptionArray(false);
        foreach ($countryList as $key => $val)
        {
            if (strtolower($val['label']) === strtolower($country_name)) {
                $country_code = $val['value'];
                break;
            }
        }

        if(!isset($country_code)){
            throw new Exception("Can't get country code of ".$country_name);
        }

        return $country_code;
    }
}