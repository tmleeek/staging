<?php

class MDN_Mpm_Helper_Pricing extends Mage_Core_Helper_Abstract
{

    private $_currency = array(
        'EUR' => '€',
        'GBP' => '£',
        'USD' => '$',
        'DKK' => 'DKK',
        'NC'  => '',
    );

    public function getColorForRepricingStatus($status)
    {
        switch($status)
        {
            case MDN_Mpm_Model_Pricer::kPricingStatusOutOfCompetition: return 'red'; break;
            case MDN_Mpm_Model_Pricer::kPricingStatusCompeteForFirstPosition: return 'green'; break;
            case MDN_Mpm_Model_Pricer::kPricingStatusCompeteNotFarFromFirstPosition: return 'orange'; break;
            case MDN_Mpm_Model_Pricer::kPricingStatusNoOffers: return 'green'; break;
        }
    }

    public function getCurrency($channelCode)
    {
        $channels = Mage::helper('Mpm/Carl')->getChannelsSubscribed();
        foreach($channels as $channel) {
            if($channelCode === $channel->organization.'_'.$channel->locale.'_'.$channel->subset) {
                return $this->_currency[$channel->currency];
            }
        }

        return '?';
    }

}
