<?php

/**
 * Class MDN_Mpm_Helper_FinalPrice
 *
 * @author    Nicolas Mugnier <contact@boostmyshop.com>
 * @copyright 2015-2016 BoostMyShop (http://www.boostmyshop.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MDN_Mpm_Helper_FinalPrice extends Mage_Core_Helper_Abstract {

    /**
     * @param array $debug
     * @param string $currency
     * @return string $html
     */
    public function getValue($debug, $currency){

        $html = 'n/a';

        if(isset($debug['end_pricing']) && $debug['end_pricing']['status'] != 'error'){

            $prices = array();
            $prices[] = $currency.' '.$debug['end_pricing']['price'];

            if($debug['end_pricing']['shipping_price']){
                $priceWithoutShipping = $debug['end_pricing']['price'] - $debug['end_pricing']['shipping_price'];
                $prices[] = $currency.' '.$priceWithoutShipping;
            }

            if($debug['end_pricing']['price'] != $debug['end_pricing']['final_price']){
                $prices[] = $currency.' '.$debug['end_pricing']['final_price'];
            }

            $html = implode('<br/>', $prices);

        }

        return $html;

    }

}