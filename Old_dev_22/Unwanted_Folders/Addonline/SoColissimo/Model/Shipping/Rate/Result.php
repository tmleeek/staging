<?php
/**
 * Addonline
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline (http://www.addonline.fr)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipping_Rate_Result 
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2014 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */
class Addonline_Socolissimo_Model_Shipping_Rate_Result extends Mage_Shipping_Model_Rate_Result
{

    /* (non-PHPdoc)
     * @see Mage_Shipping_Model_Rate_Result::sortRatesByPrice()
     */
    public function sortRatesByPrice()
    {
        if (! is_array($this->_rates) || ! count($this->_rates)) {
            return $this;
        }
        
        $rate = $this->_rates[0];
        if ($rate->carrier == 'socolissimo') {
            
            foreach ($this->_rates as $i => $rate) {
                $method = $rate->getMethod();
                $methodOrder = 9;
                if (strpos($method, 'domicile') === 0) {
                    $methodOrder = 1;
                }
                if (strpos($method, 'poste') === 0) {
                    $methodOrder = 4;
                }
                if (strpos($method, 'commercant') === 0) {
                    $methodOrder = 5;
                }
                $tmp[$i] = $methodOrder;
            }
            
            natsort($tmp);
            
            foreach ($tmp as $i => $order) {
                $result[] = $this->_rates[$i];
            }
            
            $this->reset();
            $this->_rates = $result;
            
            return $this;
        } else {
            return parent::sortRatesByPrice();
        }
    }
}
