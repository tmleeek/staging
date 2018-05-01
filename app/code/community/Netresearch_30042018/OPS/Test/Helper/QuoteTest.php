<?php
/**
 * @author      Michael Lühr <michael.luehr@netresearch.de>
 * @category    Netresearch
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2013 Netresearch GmbH & Co. KG (http://www.netresearch.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Netresearch_OPS_Test_Helper_QuoteTest extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testCleanUpOldPaymentInformation()
    {
        $payment = Mage::getModel('sales/quote_payment')->load(3);
        $this->assertArrayHasKey('cvc', $payment->getAdditionalInformation());
        Mage::helper('ops/quote')->cleanUpOldPaymentInformation();
        $payment = Mage::getModel('sales/quote_payment')->load(3);
        $this->assertArrayNotHasKey(
            'cvc', $payment->getAdditionalInformation()
        );
    }

    public function testGetQuoteCurrency()
    {
        $quote = Mage::getModel('sales/quote');
        $this->assertEquals(
            Mage::app()->getStore($quote->getStoreId())->getBaseCurrencyCode(),
            Mage::helper('ops/quote')->getQuoteCurrency($quote)
        );
        $forcedCurrency = new Varien_Object();
        $forcedCurrency->setCode('USD');
        $quote->setForcedCurrency($forcedCurrency);
        $this->assertEquals(
            'USD', Mage::helper('ops/quote')->getQuoteCurrency($quote)
        );
    }

    public function testGetPaymentActionForAuthorize()
    {
        $order   = Mage::getModel('sales/order');
        $payment = Mage::getModel('sales/order_payment');
        $order->setPayment($payment);
        $modelMock = $this->getModelMock(
            'ops/config', array('getPaymentAction')
        );
        $modelMock->expects($this->any())
            ->method('getPaymentAction')
            ->will(
                $this->returnValue(
                    'Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION'
                )
            );
        $this->replaceByMock('model', 'ops/config', $modelMock);
        $helper = Mage::helper('ops/quote');
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION,
            $helper->getPaymentAction($order)
        );
        $order->getPayment()->setAdditionalInformation(
            'PM', 'Direct Debits DE'
        );
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION,
            $helper->getPaymentAction($order)
        );
        $order->getPayment()->setAdditionalInformation(
            'PM', 'Direct Debits AT'
        );
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION,
            $helper->getPaymentAction($order)
        );
        $order->getPayment()->setAdditionalInformation(
            'PM', 'Direct Debits NL'
        );
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION,
            $helper->getPaymentAction($order)
        );
    }

    public function testGetPaymentActionForAuthorizeCapture()
    {
        $order   = Mage::getModel('sales/order');
        $payment = Mage::getModel('sales/order_payment');
        $order->setPayment($payment);
        $modelMock = $this->getModelMock(
            'ops/config', array('getPaymentAction')
        );
        $modelMock->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue('authorize_capture'));
        $this->replaceByMock('model', 'ops/config', $modelMock);
        $helper = Mage::helper('ops/quote');
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_CAPTURE_ACTION,
            $helper->getPaymentAction($order)
        );
        $order->getPayment()->setAdditionalInformation(
            'PM', 'Direct Debits DE'
        );
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_CAPTURE_ACTION,
            $helper->getPaymentAction($order)
        );
        $order->getPayment()->setAdditionalInformation(
            'PM', 'Direct Debits AT'
        );
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_CAPTURE_ACTION,
            $helper->getPaymentAction($order)
        );
        $order->getPayment()->setAdditionalInformation(
            'PM', 'Direct Debits NL'
        );
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_DIRECTDEBIT_NL,
            $helper->getPaymentAction($order)
        );
    }

}