<?php

/**
 * Class MDN_Mpm_Helper_Channel
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Helper_Channel extends Mage_Core_Helper_Abstract {

    /**
     * @param $channelCode
     * @return string
     */
    public function getChannelNameFromChannelCode($channelCode){

        list($organization, $locale, $subset) = explode('_', $channelCode);

        return ucfirst($organization).'.'.$this->getExt($locale);

    }

    /**
     * @param $locale
     * @return string
     */
    protected static function getExt($locale)
    {
        switch ($locale) {
            case 'uk':
                return 'co.uk';
            case 'us':
                return 'com';
        }

        return $locale;
    }

}