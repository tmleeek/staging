<?php
/**
 * Importedshippingmethod.php
 * CommerceExtensions @ InterSEC Solutions LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.commerceextensions.com/LICENSE-M1.txt
 *

 * @category   Orders
 * @package    Importedshippingmethod
 * @copyright  Copyright (c) 2003-2009 CommerceExtensions @ InterSEC Solutions LLC. (http://www.commerceextensions.com)
 * @license    http://www.commerceextensions.com/LICENSE-M1.txt
 */ 

class Intersec_Orderimportexport_Model_Shipping_Importedshippingmethod extends Mage_Shipping_Model_Carrier_Abstract
{
    protected $_code = 'imported';

    public static $methodQueue = array();

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $result = Mage::getModel('shipping/rate_result');

        $method = Mage::getModel('shipping/rate_result_method');

        $method->setCarrier('imported');
        $method->setCarrierTitle('Imported');

        $method->setMethod('imported');
        $method->setMethodTitle(array_shift(self::$methodQueue));

        $method->setPrice(0);
        $method->setCost(0);

        $result->append($method);

        return $result;
    }

    public function getAllowedMethods()
    {
        return array('imported'=>'imported');
    }
}