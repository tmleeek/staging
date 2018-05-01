<?php

class Netresearch_OPS_Test_Helper_Payment_RequestTest extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @return Netresearch_OPS_Helper_Payment_RequestTest
     */
    protected function getRequestHelper()
    {
        return Mage::helper('ops/payment_request');
    }

    protected function getShipToArrayKeys()
    {
        return array(
            'ECOM_SHIPTO_POSTAL_NAME_FIRST',
            'ECOM_SHIPTO_POSTAL_NAME_LAST',
            'ECOM_SHIPTO_POSTAL_STREET_LINE1',
            'ECOM_SHIPTO_POSTAL_STREET_LINE2',
            'ECOM_SHIPTO_POSTAL_COUNTRYCODE',
            'ECOM_SHIPTO_POSTAL_CITY',
            'ECOM_SHIPTO_POSTAL_POSTALCODE',
        );
    }

    public function testExtractShipToParameters()
    {
        $address = Mage::getModel('sales/quote_address');
        $params = $this->getRequestHelper()->extractShipToParameters($address);
        $this->assertTrue(is_array($params));
        foreach($this->getShipToArrayKeys() as $key) {
            $this->assertArrayHasKey($key, $params);
        }

        $address->setFirstname('Hans');
        $address->setLastname('Wurst');
        $address->setStreet('Nonnenstrasse 11d');
        $address->setCountry('DE');
        $address->setCity('Leipzig');
        $address->setPostcode('04229');
        $params = $this->getRequestHelper()->extractShipToParameters($address);
        $this->assertEquals('Hans', $params['ECOM_SHIPTO_POSTAL_NAME_FIRST']);
        $this->assertEquals('Wurst', $params['ECOM_SHIPTO_POSTAL_NAME_LAST']);
        $this->assertEquals('Nonnenstrasse 11d', $params['ECOM_SHIPTO_POSTAL_STREET_LINE1']);
        $this->assertEquals('', $params['ECOM_SHIPTO_POSTAL_STREET_LINE2']);
        $this->assertEquals('DE', $params['ECOM_SHIPTO_POSTAL_COUNTRYCODE']);
        $this->assertEquals('Leipzig', $params['ECOM_SHIPTO_POSTAL_CITY']);
        $this->assertEquals('04229', $params['ECOM_SHIPTO_POSTAL_POSTALCODE']);

    }

}