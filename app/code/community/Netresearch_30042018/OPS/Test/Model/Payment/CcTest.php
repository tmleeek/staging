<?php
class Netresearch_OPS_Test_Model_Payment_CcTest extends EcomDev_PHPUnit_Test_Case
{
    private $_model;
    private $_payment;

    public function setUp()
    {
        parent::setup();
        $this->_model = Mage::getModel('ops/payment_cc');
        $this->_payment = ObjectHandler::getObject('quoteBeforeSaveOrder')->getPayment();
    }

    public function testBrand()
    {
        $this->_payment->setAdditionalInformation('CC_BRAND', 'VISA');
        $this->assertEquals('VISA', $this->_model->getOpsBrand($this->_payment), 'VISA should have brand VISA');
        $this->assertEquals('CreditCard', $this->_model->getOpsCode($this->_payment), 'VISA should be a CreditCard');
        $this->assertTrue($this->_model->hasBrandAliasInterfaceSupport($this->_payment), 'VISA should support alias interface');

        $this->_payment->setAdditionalInformation('CC_BRAND', 'UNEUROCOM');
        $this->assertEquals('UNEUROCOM', $this->_model->getOpsBrand($this->_payment), 'UNEUROCOM should have brand UNEUROCOM');
        $this->assertEquals('UNEUROCOM', $this->_model->getOpsCode($this->_payment), 'UNEUROCOM should have code UNEUROCOM');
        $this->assertFalse($this->_model->hasBrandAliasInterfaceSupport($this->_payment), 'UNEUROCOM should NOT support alias interface');

        $this->_payment->setAdditionalInformation('CC_BRAND', 'PostFinance card');
        $this->assertEquals('PostFinance card', $this->_model->getOpsBrand($this->_payment), 'PostFinance Card should have brand "PostFinance card"');
        $this->assertEquals('PostFinance Card', $this->_model->getOpsCode($this->_payment), 'PostFinance Card should have code "PostFinance Card"');
        $this->assertFalse($this->_model->hasBrandAliasInterfaceSupport($this->_payment), 'PostFinance Card should NOT support alias interface');

        $this->_payment->setAdditionalInformation('CC_BRAND', 'PRIVILEGE');
        $this->assertEquals('PRIVILEGE', $this->_model->getOpsBrand($this->_payment), 'PRIVILEGE should have brand PRIVILEGE');
        $this->assertEquals('CreditCard', $this->_model->getOpsCode($this->_payment), 'PRIVILEGE should be a CreditCard');
        $this->assertFalse($this->_model->hasBrandAliasInterfaceSupport($this->_payment), 'PRIVILEGE should NOT support alias interface');
    }

    public function testOrderPlaceRedirectUrl()
    {
        $this->_payment->setAdditionalInformation('CC_BRAND', 'VISA');
        $this->assertFalse($this->_model->getOrderPlaceRedirectUrl($this->_payment), 'VISA should NOT require a redirect after checkout');
        
        $this->_payment->setAdditionalInformation('CC_BRAND', 'VISA');
        $this->_payment->setAdditionalInformation('HTML_ANSWER', 'BASE64ENCODEDSTRING');
        $this->assertInternalType('string', $this->_model->getOrderPlaceRedirectUrl($this->_payment), 'If Brand is VIA and HTML_ANSWER isset, a redirect should happen after checkout');
        
        $this->_payment->setAdditionalInformation('CC_BRAND', 'PRIVILEGE');
        $this->assertInternalType('string', $this->_model->getOrderPlaceRedirectUrl($this->_payment), 'PRIVILEGE should require a redirect after checkout');
        
    }
}

