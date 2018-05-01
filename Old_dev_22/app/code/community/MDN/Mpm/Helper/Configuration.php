<?php

class MDN_Mpm_Helper_Configuration extends Mage_Core_Helper_Abstract {

    public function getSubscribedChannelsWithApi(){

        $allowed = array('cdiscount_fr_default', 'priceminister_fr_default', 'fnac_fr_default');
        $subscribedChannelsWithApi = array();

        $amazonUS = array('us','ca');
        $amazonEU = array('fr','es','it','de','uk');

        $amazonGroups = array('amazon_us' => false, 'amazon_eu' => false);

        foreach($this->getChannelsSubscribed(true) as $k => $v){

            list($organization, $locale, $subset) = explode('_',$k);
            if($organization == 'amazon'){

                if(in_array($locale, $amazonEU) && $amazonGroups['amazon_eu'] === false){

                    $subscribedChannelsWithApi[$k] = $v;
                    $amazonGroups['amazon_eu'] = true;

                }

                if(in_array($locale, $amazonUS) && $amazonGroups['amazon_us'] === false){

                    $subscribedChannelsWithApi[$k] = $v;
                    $amazonGroups['amazon_us'] = true;

                }

            }else{

                if(in_array($k, $allowed)){

                    $subscribedChannelsWithApi[$k] = $v;

                }
            }

        }

        return $subscribedChannelsWithApi;

    }

    public function getChannelsSubscribed($toOptionList = false){

        return Mage::Helper('Mpm/Carl')->getChannelsSubscribed($toOptionList);

    }

}