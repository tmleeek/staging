<?php
class Netresearch_OPS_Test_Helper_PaymentTest
    extends EcomDev_PHPUnit_Test_Case
{
    private $_helper;
    private $store;

    public function setUp()
    {
        parent::setup();
        $this->_helper = Mage::helper('ops/payment');
        $this->store = Mage::app()->getStore(0)->load(0);
        $this->store->resetConfig();
    }

    public function testIsPaymentAuthorizeType()
    {
        $this->assertTrue(
            $this->_helper->isPaymentAuthorizeType(
                Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED
            )
        );
        $this->assertTrue(
            $this->_helper->isPaymentAuthorizeType(
                Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING
            )
        );
        $this->assertTrue(
            $this->_helper->isPaymentAuthorizeType(
                Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_UNKNOWN
            )
        );
        $this->assertTrue(
            $this->_helper->isPaymentAuthorizeType(
                Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT
            )
        );
        $this->assertFalse($this->_helper->isPaymentAuthorizeType(0));
    }

    public function testIsPaymentCaptureType()
    {
        $this->assertTrue(
            $this->_helper->isPaymentCaptureType(
                Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED
            )
        );
        $this->assertTrue(
            $this->_helper->isPaymentCaptureType(
                Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSING
            )
        );
        $this->assertTrue(
            $this->_helper->isPaymentCaptureType(
                Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_UNCERTAIN
            )
        );
        $this->assertFalse($this->_helper->isPaymentCaptureType(0));
    }

    /**
     * send no invoice mail if it is denied by configuration
     */
    public function testSendNoInvoiceToCustomerIfDenied()
    {
        $this->store->setConfig('payment_services/ops/send_invoice', 0);
        $this->assertFalse(Mage::getModel('ops/config')->getSendInvoice());
        $sentInvoice = $this->getModelMock(
            'sales/order_invoice', array('getEmailSent', 'sendEmail')
        );
        $sentInvoice->expects($this->any())
            ->method('getEmailSent')
            ->will($this->returnValue(false));
        $sentInvoice->expects($this->never())
            ->method('sendEmail');
        $this->_helper->sendInvoiceToCustomer($sentInvoice);
    }

    /**
     * send no invoice mail if it was already sent
     */
    public function testSendNoInvoiceToCustomerIfAlreadySent()
    {
        $this->store->setConfig('payment_services/ops/send_invoice', 1);
        $this->assertTrue(Mage::getModel('ops/config')->getSendInvoice());
        $someInvoice = $this->getModelMock(
            'sales/order_invoice', array('getEmailSent', 'sendEmail')
        );
        $someInvoice->expects($this->any())
            ->method('getEmailSent')
            ->will($this->returnValue(true));
        $someInvoice->expects($this->never())
            ->method('sendEmail');
        $this->_helper->sendInvoiceToCustomer($someInvoice);
    }

    /**
     * send invoice mail
     */
    public function testSendInvoiceToCustomerIfEnabled()
    {
        $this->store->setConfig('payment_services/ops/send_invoice', 1);
        $this->assertTrue(Mage::getModel('ops/config')->getSendInvoice());
        $anotherInvoice = $this->getModelMock(
            'sales/order_invoice', array('getEmailSent', 'sendEmail')
        );
        $anotherInvoice->expects($this->any())
            ->method('getEmailSent')
            ->will($this->returnValue(false));
        $anotherInvoice->expects($this->once())
            ->method('sendEmail')
            ->with($this->equalTo(true));
        $this->_helper->sendInvoiceToCustomer($anotherInvoice);
    }

    public function testPrepareParamsAndSort()
    {
        $params = array(
            'CVC'          => '123',
            'CARDNO'       => '4111111111111111',
            'CN'           => 'JohnSmith',
            'PSPID'        => 'test1',
            'ED'           => '1212',
            'ACCEPTURL'    => 'https=//www.myshop.com/ok.html',
            'EXCEPTIONURL' => 'https=//www.myshop.com/nok.html',
            'BRAND'        => 'VISA',
        );
        $sortedParams = array(
            'ACCEPTURL'    => array('key'   => 'ACCEPTURL',
                                    'value' => 'https=//www.myshop.com/ok.html'),
            'BRAND'        => array('key' => 'BRAND', 'value' => 'VISA'),
            'CARDNO'       => array('key'   => 'CARDNO',
                                    'value' => '4111111111111111'),
            'CN'           => array('key' => 'CN', 'value' => 'JohnSmith'),
            'CVC'          => array('key' => 'CVC', 'value' => '123'),
            'ED'           => array('key' => 'ED', 'value' => '1212'),
            'EXCEPTIONURL' => array('key'   => 'EXCEPTIONURL',
                                    'value' => 'https=//www.myshop.com/nok.html'),
            'PSPID'        => array('key' => 'PSPID', 'value' => 'test1'),
        );
        $secret = 'Mysecretsig1875!?';
        $shaInSet
            =
            'ACCEPTURL=https=//www.myshop.com/ok.htmlMysecretsig1875!?BRAND=VISAMysecretsig1875!?'
                . 'CARDNO=4111111111111111Mysecretsig1875!?CN=JohnSmithMysecretsig1875!?CVC=123Mysecretsig1875!?'
                . 'ED=1212Mysecretsig1875!?EXCEPTIONURL=https=//www.myshop.com/nok.htmlMysecretsig1875!?'
                . 'PSPID=test1Mysecretsig1875!?';
        $key = 'a28dc9fe69b63fe81da92471fefa80aca3f4851a';
        $this->assertEquals(
            $sortedParams, $this->_helper->prepareParamsAndSort($params)
        );
        $this->assertEquals(
            $shaInSet, $this->_helper->getSHAInSet($params, $secret)
        );
        $this->assertEquals($key, $this->_helper->shaCrypt($shaInSet, $secret));
    }

    public function testHandleUnknownStatus()
    {
        $order = $this->getModelMock('sales/order', array('save'));
        $order->expects($this->any())
            ->method('save')
            ->will($this->returnValue(true));
        $order->setState(
            Mage_Sales_Model_Order::STATE_NEW,
            Mage_Sales_Model_Order::STATE_NEW
        );
        $statusHistoryCount = $order->getStatusHistoryCollection()->count();
        Mage::helper('ops/payment')->handleUnknownStatus($order);
        $this->assertEquals(
            Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, $order->getState()
        );
        $this->assertTrue(
            $statusHistoryCount < $order->getStatusHistoryCollection()->count()
        );
        $statusHistoryCount = $order->getStatusHistoryCollection()->count();
        $order->setState(
            Mage_Sales_Model_Order::STATE_PROCESSING,
            Mage_Sales_Model_Order::STATE_PROCESSING
        );

        Mage::helper('ops/payment')->handleUnknownStatus($order);
        $this->assertEquals(
            Mage_Sales_Model_Order::STATE_PROCESSING, $order->getState()
        );
        $this->assertTrue(
            $statusHistoryCount < $order->getStatusHistoryCollection()->count()
        );
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     * @loadFixture ../../../var/fixtures/quotes.yaml
     */
    public function testGetBaseGrandTotalFromSalesObject()
    {
        $helper = Mage::helper('ops/payment');
        $order = Mage::getModel('sales/order')->load(14);
        $amount = $helper->getBaseGrandTotalFromSalesObject($order);
        $this->assertEquals($order->getBaseGrandTotal(), $amount);
        $order = Mage::getModel('sales/order')->load(15);
        $amount = $helper->getBaseGrandTotalFromSalesObject($order);
        $this->assertEquals($order->getBaseGrandTotal(), $amount);
        $quote = Mage::getModel('sales/quote')->load(1);
        $amount = $helper->getBaseGrandTotalFromSalesObject($quote);
        $this->assertEquals($quote->getBaseGrandTotal(), $amount);
        $quote = Mage::getModel('sales/quote')->load(2);
        $amount = $helper->getBaseGrandTotalFromSalesObject($quote);
        $this->assertEquals($quote->getBaseGrandTotal(), $amount);
        $someOtherObject = new Varien_Object();
        $this->setExpectedException('Mage_Core_Exception');
        $helper->getBaseGrandTotalFromSalesObject($someOtherObject);
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testSaveOpsRefundOperationCodeToPayment()
    {
        $order = Mage::getModel('sales/order')->load(11);
        $payment = $order->getPayment();
        $helper = Mage::helper('ops/payment');

        // no last refund operation code is set if an empty string is passed
        $helper->saveOpsRefundOperationCodeToPayment($payment, '');
        $this->assertFalse(
            array_key_exists(
                'lastRefundOperationCode', $payment->getAdditionalInformation()
            )
        );

        // no last refund operation code is set if it's no refund operation code
        $helper->saveOpsRefundOperationCodeToPayment(
            $payment, Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_FULL
        );
        $this->assertFalse(
            array_key_exists(
                'lastRefundOperationCode', $payment->getAdditionalInformation()
            )
        );

        // last ops refund code is present if a valid refund code is passed
        $helper->saveOpsRefundOperationCodeToPayment(
            $payment, Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_PARTIAL
        );
        $this->assertTrue(
            array_key_exists(
                'lastRefundOperationCode', $payment->getAdditionalInformation()
            )
        );
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_PARTIAL,
            $payment->getAdditionalInformation('lastRefundOperationCode')
        );

        // last ops refund code is present if a valid refund code is passed and will override a previous one
        $helper->saveOpsRefundOperationCodeToPayment(
            $payment, Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL
        );
        $this->assertTrue(
            array_key_exists(
                'lastRefundOperationCode', $payment->getAdditionalInformation()
            )
        );
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL,
            $payment->getAdditionalInformation('lastRefundOperationCode')
        );
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testSetCanRefundToPayment()
    {

        $helper = Mage::helper('ops/payment');
        $order = Mage::getModel('sales/order')->load(11);
        $payment = $order->getPayment();
        $helper->setCanRefundToPayment($payment);
        $this->assertFalse(
            array_key_exists('canRefund', $payment->getAdditionalInformation())
        );

        $order = Mage::getModel('sales/order')->load(15);
        $payment = $order->getPayment();
        $helper->setCanRefundToPayment($payment);
        $this->assertTrue($payment->getAdditionalInformation('canRefund'));

        $order = Mage::getModel('sales/order')->load(16);
        $payment = $order->getPayment();
        $helper->setCanRefundToPayment($payment);
        $this->assertFalse($payment->getAdditionalInformation('canRefund'));
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     *
     */
    public function testSetPaymentTransactionInformation()
    {
        $order = Mage::getModel('sales/order')->load(15);
        $reflectionClass = new ReflectionClass(get_class(
            Mage::helper('ops/payment')
        ));
        $method = $reflectionClass->getMethod(
            'setPaymentTransactionInformation'
        );
        $method->setAccessible(true);
        $paymentHelper = Mage::helper('ops/payment');
        $params = array(
            'PAYID'  => '0815',
            'STATUS' => 9
        );
        $method->invoke(
            $paymentHelper, $order->getPayment(), $params, 'accept'
        );
        $this->assertEquals(
            '0815', $order->getPayment()->getAdditionalInformation('paymentId')
        );
        $this->assertEquals(
            9, $order->getPayment()->getAdditionalInformation('status')
        );

        $params = array(
            'PAYID'      => '0815',
            'STATUS'     => 9,
            'ACCEPTANCE' => ''
        );
        $method->invoke(
            $paymentHelper, $order->getPayment(), $params, 'accept'
        );
        $this->assertEquals(
            '0815', $order->getPayment()->getAdditionalInformation('paymentId')
        );
        $this->assertEquals(
            '', $order->getPayment()->getAdditionalInformation('acceptance')
        );

        $params = array(
            'PAYID'      => '0815',
            'STATUS'     => 9,
            'ACCEPTANCE' => 'Akzeptanz'
        );
        $method->invoke(
            $paymentHelper, $order->getPayment(), $params, 'accept'
        );
        $this->assertEquals(
            '0815', $order->getPayment()->getAdditionalInformation('paymentId')
        );
        $this->assertEquals(
            'Akzeptanz',
            $order->getPayment()->getAdditionalInformation('acceptance')
        );

        $params = array(
            'PAYID'       => '0815',
            'STATUS'      => 9,
            'ACCEPTANCE'  => 'Akzeptanz',
            'HTML_ANSWER' => '3D Secure'
        );
        $method->invoke(
            $paymentHelper, $order->getPayment(), $params, 'accept'
        );
        $this->assertEquals(
            '0815', $order->getPayment()->getAdditionalInformation('paymentId')
        );
        $this->assertEquals(
            'Akzeptanz',
            $order->getPayment()->getAdditionalInformation('acceptance')
        );
        $this->assertEquals(
            '3D Secure',
            $order->getPayment()->getAdditionalInformation('HTML_ANSWER')
        );
    }


    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     *
     */
    public function testApplyStateForOrder()
    {
        $order = Mage::getModel('sales/order')->load(19);
        $paymenthelperMock = $this->getHelperMock(
            'ops/payment', array(
                                'acceptOrder',
                                'waitOrder',
                                'declineOrder',
                                'cancelOrder',
                                'handleException'
                           )
        );

        // assertion for OPS_OPEN_INVOICE_DE_PROCESSED = 41000001
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT)
            )
        );

        // assertion for OPS_WAITING_FOR_IDENTIFICATION  = 46
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_WAITING_FOR_IDENTIFICATION)
            )
        );

        // assertion for OPS_AUTHORIZED  = 5
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED)
            )
        );
        // assertion for OPS_AUTHORIZED_KWIXO  = 50
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_KWIXO)
            )
        );

        // assertion for OPS_AUTHORIZED_WAITING = 51
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_WAITING)
            )
        );

        // assertion for OPS_AUTHORIZED_UNKNOWN  = 52
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED_UNKNOWN)
            )
        );

        // assertion for OPS_AWAIT_CUSTOMER_PAYMENT = 41
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_AWAIT_CUSTOMER_PAYMENT)
            )
        );

        // assertion for OPS_PAYMENT_REQUESTED = 9
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED)
            )
        );
        // assertion for OPS_PAYMENT_PROCESSING = 91
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_PROCESSING)
            )
        );
        // assertion for OPS_OPEN_INVOICE_DE_PROCESSED  = 41000001
        $this->assertEquals(
            'accept', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_OPEN_INVOICE_DE_PROCESSED)
            )
        );
        // assertion for OPS_AUTH_REFUSED   = 2
        $this->assertEquals(
            'decline', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_AUTH_REFUSED)
            )
        );
        // assertion for OPS_PAYMENT_REFUSED = 93
        $this->assertEquals(
            'decline', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REFUSED)
            )
        );
        // assertion for OPS_PAYMENT_CANCELED_BY_CUSTOMER        = 1
        $this->assertEquals(
            'cancel', $paymenthelperMock->applyStateForOrder(
                $order,
                array(
                     'STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_CANCELED_BY_CUSTOMER,
                     'PAYID'  => 4711
                )
            )
        );
        // assertion for exception case
        $this->assertEquals(
            'exception', $paymenthelperMock->applyStateForOrder(
                $order,
                array('STATUS' => 'default')
            )
        );
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testForceAuthorize()
    {
        $helper = Mage::helper('ops/payment');
        $reflectionClass = new ReflectionClass(get_class($helper));
        $method = $reflectionClass->getMethod("forceAuthorize");
        $method->setAccessible(true);

        $order = Mage::getModel('sales/order')->load(11);
        $this->assertFalse($method->invoke($helper, $order));

        $order = Mage::getModel('sales/order')->load(27);
        $this->assertTrue($method->invoke($helper, $order));

        $order = Mage::getModel('sales/order')->load(28);
        $this->assertTrue($method->invoke($helper, $order));

//        $order = Mage::getModel('sales/order')->load(29);
//        $this->assertTrue($method->invoke($helper, $order));
    }
}