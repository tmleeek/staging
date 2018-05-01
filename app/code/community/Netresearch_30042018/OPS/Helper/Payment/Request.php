<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de> 
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2014 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Netresearch_OPS_Helper_Payment_Request
{
    /**
     * extracts the ship to information from a given address
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     *
     * @return array - the parameters containing the ship to data
     */
    public function extractShipToParameters(Mage_Customer_Model_Address_Abstract $address)
    {
        $paramValues = array();
        $paramValues['ECOM_SHIPTO_POSTAL_NAME_FIRST'] = $address->getFirstname();
        $paramValues['ECOM_SHIPTO_POSTAL_NAME_LAST'] = $address->getLastname();
        $paramValues['ECOM_SHIPTO_POSTAL_STREET_LINE1'] = $address->getStreet(1);
        $paramValues['ECOM_SHIPTO_POSTAL_STREET_LINE2'] = $address->getStreet(2);
        $paramValues['ECOM_SHIPTO_POSTAL_COUNTRYCODE'] = $address->getCountry();
        $paramValues['ECOM_SHIPTO_POSTAL_CITY'] = $address->getCity();
        $paramValues['ECOM_SHIPTO_POSTAL_POSTALCODE'] = $address->getPostcode();

        return $paramValues;
    }
} 