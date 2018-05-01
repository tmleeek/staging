<?php
/**
 * Gls_Unibox extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Gls
 * @package    Gls_Unibox
 * @copyright  Copyright (c) 2013 webvisum GmbH
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Webvisum
 * @package    Gls_Unibox
 */
class Gls_Unibox_Model_Shipping_Carrier_Gls extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'gls';
    
    public function isActive()
    {
        return true;
    }
    
    public function isTrackingAvailable()
    {
        return true;
    }
    
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return Mage::getModel('shipping/rate_result');
    }
    
    public function getAllowedMethods()
    {
        return array($this->_code => 'gls');
    }
}