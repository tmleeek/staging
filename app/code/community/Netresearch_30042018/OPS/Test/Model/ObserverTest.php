<?php

class Netresearch_OPS_Test_Model_ObserverTest extends EcomDev_PHPUnit_Test_Case_Controller
{
    private $_model;

    public function setUp()
    {
        parent::setup();
        $this->_model = Mage::getModel('ops/observer');
    }

    public function testType()
    {
        $this->assertInstanceOf('Netresearch_OPS_Model_Observer', $this->_model);
    }

    public function testIsCheckoutWithAliasOrDd()
    {
        if (version_compare(PHP_VERSION, '5.3.2') >= 0) {
            $class = new ReflectionClass('Netresearch_OPS_Model_Observer');
            $method = $class->getMethod('isCheckoutWithAliasOrDd');
            $method->setAccessible(true);

            $this->assertTrue($method->invokeArgs($this->_model, array('ops_cc')));
            $this->assertTrue($method->invokeArgs($this->_model, array('ops_directDebit')));
            $this->assertFalse($method->invokeArgs($this->_model, array('checkmo')));
        }
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testIsInlinePayment()
    {
        $class = new ReflectionClass('Netresearch_OPS_Model_Observer');
        $method = $class->getMethod('isInlinePayment');
        $method->setAccessible(true);

        $configMock = $this->getModelMock('ops/config', array('getInlinePaymentCcTypes'));
        $configMock->expects($this->any())
            ->method('getInlinePaymentCcTypes')
            ->will($this->returnValue(array('visa')));

        // direct debit should return true
        $order = Mage::getModel('sales/order')->load(21);
        $this->assertTrue($method->invokeArgs($this->_model, array($order->getPayment())));

        // credit card with inline mode should return true
        $order = Mage::getModel('sales/order')->load(24);
        $this->assertTrue($method->invokeArgs($this->_model, array($order->getPayment())));

        // credit card without Alias support should return false
        $order = Mage::getModel('sales/order')->load(25);
        $this->assertFalse($method->invokeArgs($this->_model, array($order->getPayment())));

        $order = Mage::getModel('sales/order')->load(26);
        $this->assertFalse($method->invokeArgs($this->_model, array($order->getPayment())));
    }

    public function testPerformDirectLinkRequestWithUnknownResponse()
    {
        $quote = $this->getModelMock('sales/quote', array('save'));
        $aliasHelperMock = $this->getHelperMock('ops/alias', array('setAliasActive'));
        $this->replaceByMock('helper', 'ops/alias', $aliasHelperMock);
        $payment = $this->getModelMock('sales/quote_payment', array('save'));
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnValue(null));
        $this->replaceByMock('model', 'sales/quote_payment', $payment);
        $quote->setPayment($payment);
        $response = null;
        $directLinkMock = $this->getModelMock('ops/api_directlink', array('performRequest'));
        $directLinkMock->expects($this->any())
            ->method('performRequest')
            ->will($this->returnValue($response));
        $this->replaceByMock('model', 'ops/api_directlink', $directLinkMock);
        $observer = Mage::getModel('ops/observer');
        $observer->performDirectLinkRequest($quote, array());
        $this->assertFalse($this->setExpectedException('PHPUnit_Framework_ExpectationFailedException'));
        $this->assertTrue(array_key_exists('ops_response', $quote->getPayment()->getAdditionalInformation()));
    }

    public function testPerformDirectLinkRequestWithInvalidResponse()
    {
        $quote = new Varien_Object();
        $aliasHelperMock = $this->getHelperMock('ops/alias', array('setAliasActive'));
        $this->replaceByMock('helper', 'ops/alias', $aliasHelperMock);
        $payment = $this->getModelMock('sales/quote_payment', array('save'));
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnValue(null));
        $this->replaceByMock('model', 'sales/quote_payment', $payment);
        $quote->setPayment($payment);
        $response = '';
        $directLinkMock = $this->getModelMock('ops/api_directlink', array('performRequest'));
        $directLinkMock->expects($this->any())
            ->method('performRequest')
            ->will($this->returnValue($response));
        $this->replaceByMock('model', 'ops/api_directlink', $directLinkMock);
        $observer = Mage::getModel('ops/observer');
        $this->assertTrue($this->setExpectedException('PHPUnit_Framework_ExpectationFailedException'));
        $observer->performDirectLinkRequest($quote, array());
        $this->assertFalse(array_key_exists('ops_response', $quote->getPayment()->getAdditionalInformation()));
    }

    public function testPerformDirectLinkRequestWithValidResponse()
    {
        $quote = new Varien_Object();
        $aliasHelperMock = $this->getHelperMock('ops/alias', array('setAliasActive'));
        $this->replaceByMock('helper', 'ops/alias', $aliasHelperMock);
        $payment = $this->getModelMock('sales/quote_payment', array('save'));
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnValue(null));
        $this->replaceByMock('model', 'sales/quote_payment', $payment);
        $quote->setPayment($payment);
        $response = array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZED);
        $directLinkMock = $this->getModelMock('ops/api_directlink', array('performRequest'));
        $directLinkMock->expects($this->any())
            ->method('performRequest')
            ->will($this->returnValue($response));
        $this->replaceByMock('model', 'ops/api_directlink', $directLinkMock);
        $observer = Mage::getModel('ops/observer');
        $this->assertFalse($this->setExpectedException('PHPUnit_Framework_ExpectationFailedException'));
        $observer->performDirectLinkRequest($quote, array());
        $this->assertTrue(array_key_exists('ops_response', $quote->getPayment()->getAdditionalInformation()));
    }

    public function testPerformDirectLinkRequestWithValidResponseButInvalidStatus()
    {
        $quote = new Varien_Object();
        $aliasHelperMock = $this->getHelperMock('ops/alias', array('setAliasActive'));
        $this->replaceByMock('helper', 'ops/alias', $aliasHelperMock);
        $payment = $this->getModelMock('sales/quote_payment', array('save'));
        $payment->expects($this->any())
            ->method('save')
            ->will($this->returnValue(null));
        $this->replaceByMock('model', 'sales/quote_payment', $payment);
        $quote->setPayment($payment);
        $response = array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_AUTH_REFUSED);
        $directLinkMock = $this->getModelMock('ops/api_directlink', array('performRequest'));
        $directLinkMock->expects($this->any())
            ->method('performRequest')
            ->will($this->returnValue($response));
        $this->replaceByMock('model', 'ops/api_directlink', $directLinkMock);
        $observer = Mage::getModel('ops/observer');
        $this->assertTrue($this->setExpectedException('PHPUnit_Framework_ExpectationFailedException'));
        $observer->performDirectLinkRequest($quote, array());
        $this->assertFalse(array_key_exists('ops_response', $quote->getPayment()->getAdditionalInformation()));
    }

    public function testAppendCheckBoxToRefundForm()
    {
        $sessionMock = $this->getModelMock('core/session', array('init', 'save'));
        $this->replaceByMock('model', 'core/session', $sessionMock);

        Mage::register('current_creditmemo', null, true);
        $transport = new Varien_Object();
        $transport->setHtml('Foo');
        $observer = Mage::getModel('ops/observer');
        $event = new Varien_Object();
        $event->setBlock('');
        $this->assertEquals('', $observer->appendCheckBoxToRefundForm($event));

        $order = new Varien_Object();
        $payment = new Varien_Object();
        $methodInstance = Mage::getModel('ops/payment_cc');
        $payment->setMethodInstance($methodInstance);
        $order->setPayment($payment);
        $invoice = new Varien_Object();
        $invoice->setTransactionId(1);
        $creditMemo = $this->getModelMock('sales/order_creditmemo', array('getOrder', 'getInvoice', 'canRefund', 'getOrderId'));
        $creditMemo->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));
        $creditMemo->expects($this->any())
            ->method('getInvoice')
            ->will($this->returnValue($invoice));
        $creditMemo->expects($this->any())
            ->method('canRefund')
            ->will($this->returnValue(true));
        $creditMemo->expects($this->any())
            ->method('getOrderId')
            ->will($this->returnValue(1));
        Mage::register('current_creditmemo', $creditMemo, true);
        $block = Mage::app()->getLayout()->getBlockSingleton('adminhtml/sales_order_creditmemo_totals');
        $parentBlock = Mage::app()->getLayout()->getBlockSingleton('adminhtml/sales_order_creditmemo_create_items');
        $block->setParentBlock($parentBlock);

        $blockMock = $this->getBlockMock('ops/adminhtml_sales_order_creditmemo_totals_checkbox', array('renderView'));
        $blockMock->expects($this->once())
            ->method('renderView')
            ->will($this->returnValue('<b>checkbox</b>'));
        $this->replaceByMock('block', 'ops/adminhtml_sales_order_creditmemo_totals_checkbox', $blockMock);
        $event->setBlock($block);
        $event->setTransport($transport);
        $html = $observer->appendCheckBoxToRefundForm($event);
        $this->assertEquals('Foo<b>checkbox</b>', $html);
        $this->assertNotEquals('Bar<span>checkbox</span>', $html);

        Mage::unregister('current_creditmemo');
    }

    public function testShowWarningForClosedTransactions()
    {
        Mage::register('current_creditmemo', null);
        $transport = new Varien_Object();
        $transport->setHtml('Foo');
        $observer = Mage::getModel('ops/observer');
        $event = new Varien_Object();
        $event->setBlock('');
        $this->assertEquals('', $observer->showWarningForClosedTransactions($event));

        $order = new Varien_Object();
        $payment = new Varien_Object();
        $methodInstance = Mage::getModel('ops/payment_cc');
        $payment->setMethodInstance($methodInstance);
        $order->setPayment($payment);
        $invoice = new Varien_Object();
        $invoice->setTransactionId(1);
        $creditMemo = $this->getModelMock('sales/order_creditmemo', array('getOrder', 'getInvoice', 'canRefund', 'getOrderId'));
        $creditMemo->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($order));
        $creditMemo->expects($this->any())
            ->method('getInvoice')
            ->will($this->returnValue($invoice));
        $creditMemo->expects($this->any())
            ->method('canRefund')
            ->will($this->returnValue(false));
        $creditMemo->expects($this->any())
            ->method('getOrderId')
            ->will($this->returnValue(1));
        Mage::register('current_creditmemo', $creditMemo);
        $block = Mage::app()->getLayout()->getBlockSingleton('adminhtml/sales_order_creditmemo_create');

        $blockMock = $this->getBlockMock('ops/adminhtml_sales_order_creditmemo_closedTransaction_warning', array('renderView'));
        $blockMock->expects($this->once())
            ->method('renderView')
            ->will($this->returnValue('<b>warning</b>'));
        $this->replaceByMock('block', 'ops/adminhtml_sales_order_creditmemo_closedTransaction_warning', $blockMock);
        $event->setBlock($block);
        $event->setTransport($transport);
        $html = $observer->showWarningForClosedTransactions($event);
        $this->assertEquals('<b>warning</b>Foo', $html);
        $this->assertNotEquals('Bar<span>warning</span>', $html);

        Mage::unregister('current_creditmemo');
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testConfirmAliasPayment()
    {
        $quote = Mage::getModel('sales/quote')->load(12);
        $order = Mage::getModel('sales/order')->load(11);
        $payment = $quote->getPayment();
        $payment->setAdditionalInformation(array('cvc' => '123', 'alias' => '99'));
        $quote->setPayment($payment);
        $requestParams = $this->getRequestParamsWithAlias($quote);

        $helperMock = $this->getHelperMock('ops/data', array('isAdminSession'));
        $helperMock->expects($this->any())
            ->method('isAdminSession')
            ->will($this->returnValue(true));
        $this->replaceByMock('helper', 'ops/data', $helperMock);
        $observerMock = $this->getModelMock('ops/observer', array('performDirectLinkRequest', 'getQuoteCurrency'));
        $observerMock->expects($this->any())
            ->method('performDirectLinkRequest')
            ->with($quote, $requestParams, 1)
            ->will($this->returnValue('WuselDusel'));

        $observerMock->expects($this->any())
            ->method('getQuoteCurrency')
            ->will($this->returnValue('USD'));

        $orderHelperMock = $this->getHelperMock('ops/order', array('checkIfAddressesAreSame'));
        $orderHelperMock->expects($this->any())
            ->method('checkIfAddressesAreSame')
            ->will($this->returnValue(1));
        $this->replaceByMock('helper', 'ops/order', $orderHelperMock);

        $customerSessionMock = $this->getModelMock('customer/session', array('isLoggedIn'));
        $customerSessionMock->expects($this->any())
            ->method('isLoggedIn')
            ->will($this->returnValue(1));
        $this->replaceByMock('model', 'customer/session', $customerSessionMock);

        $configModelMock = $this->getModelMock('ops/config', array(
            'get3dSecureIsActive',
            'getAcceptUrl',
            'getDeclineUrl',
            'getExceptionUrl'
            )
        );

        $configModelMock->expects($this->any())
            ->method('get3dSecureIsActive')
            ->will($this->returnValue(true));
        $configModelMock->expects($this->any())
            ->method('getAcceptUrl')
            ->will($this->returnValue('www.abc.com'));
        $configModelMock->expects($this->any())
            ->method('getDeclineUrl')
            ->will($this->returnValue('www.abcd.com'));
        $configModelMock->expects($this->any())
            ->method('getExceptionUrl')
            ->will($this->returnValue('www.abcde.com'));
        $this->replaceByMock('model', 'ops/config', $configModelMock);

        $aliashelperMock = $this->getHelperMock('ops/alias', array('getAlias', 'cleanUpAdditionalInformation'));
        $aliashelperMock->expects($this->any())
            ->method('getAlias')
            ->with($quote)
            ->will($this->returnValue('99'));
        $this->replaceByMock('helper', 'ops/alias', $aliashelperMock);
        $this->assertEquals('WuselDusel', $observerMock->confirmAliasPayment($order, $quote));

        $helperMock = $this->getHelperMock('ops/data', array('isAdminSession'));
        $helperMock->expects($this->any())
            ->method('isAdminSession')
            ->will($this->returnValue(true));
        $this->replaceByMock('helper', 'ops/data', $helperMock);

        $observerMock = $this->getModelMock('ops/observer', array('performDirectLinkRequest', 'getQuoteCurrency'));

        $observerMock->expects($this->any())
            ->method('getQuoteCurrency')
            ->will($this->returnValue('USD'));

        $requestParams = $this->getRequestParamsWithoutAlias($quote);
        $observerMock->expects($this->any())
            ->method('performDirectLinkRequest')
            ->with($quote, $requestParams, 1)
            ->will($this->returnValue('wrong'));
        $this->assertEquals('wrong', $observerMock->confirmAliasPayment($order, $quote));

    }


    private function getRequestParamsWithAlias($quote)
    {
           return  array(
                'ALIAS' => '99',
                'AMOUNT' => 0.0,
                'CURRENCY' => 'USD',
                'OPERATION' => 'RES',
                'ORDERID' => Mage::getSingleton('ops/config')->getConfigData('devprefix') . $quote->getId(),
                'EMAIL' => 'hubertus.von.fuerstenberg@trash-mail.com',
                'OWNERADDRESS' => utf8_decode('An der Tabaksmühle 3a'),
                'OWNERZIP' => '04229',
                'OWNERTELNO' => null,
                'OWNERCTY' => 'DE',
                'ADDMATCH' => 1,
                'ECOM_SHIPTO_POSTAL_POSTALCODE' => '04229',
                'ECOM_BILLTO_POSTAL_POSTALCODE' => '04229',
                'CVC' => '123',
                'REMOTE_ADDR' => 'NONE',
                'CUID' => null,
                'ECI' => Netresearch_OPS_Model_Eci_Values::MANUALLY_KEYED_FROM_MOTO,
                'OWNERTOWN' => 'Leipzig',
                'ORIG' => Mage::helper('ops/data')->getModuleVersionString(),
                'ECOM_SHIPTO_POSTAL_NAME_FIRST' => 'Hubertus',
                'ECOM_SHIPTO_POSTAL_NAME_LAST' => utf8_decode('Fürstenberg'),
                'ECOM_SHIPTO_POSTAL_STREET_LINE1' => utf8_decode('An der Tabaksmühle 3a'),
                'ECOM_SHIPTO_POSTAL_STREET_LINE2' => '',
                'ECOM_SHIPTO_POSTAL_COUNTRYCODE' => 'DE',
                'ECOM_SHIPTO_POSTAL_CITY' => 'Leipzig',
            );
    }
    
    private function getRequestParamsWithoutAlias($quote)
    {
        return array(
            'ALIAS' => '99',
            'AMOUNT' => 0.0,
            'CURRENCY' => 'USD',
            'OPERATION' => 'RES',
            'ORDERID' => Mage::getSingleton('ops/config')->getConfigData('devprefix') . $quote->getId(),
            'EMAIL' => 'hubertus.von.fuerstenberg@trash-mail.com',
            'OWNERADDRESS' => utf8_decode('An der Tabaksmühle 3a'),
            'OWNERZIP' => '04229',
            'OWNERTELNO' => null,
            'OWNERCTY' => 'DE',
            'ADDMATCH' => 1,
            'ECOM_SHIPTO_POSTAL_POSTALCODE' => '04229',
            'ECOM_BILLTO_POSTAL_POSTALCODE' => '04229',
            'CVC' => '123',
            'REMOTE_ADDR' => 'NONE',
            'OWNERTOWN' => 'Leipzig',
            'ORIG' => Mage::helper('ops/data')->getModuleVersionString(),
            'ECOM_SHIPTO_POSTAL_NAME_FIRST' => 'Hubertus',
            'ECOM_SHIPTO_POSTAL_NAME_LAST' => utf8_decode('Fürstenberg'),
            'ECOM_SHIPTO_POSTAL_STREET_LINE1' => utf8_decode('An der Tabaksmühle 3a'),
            'ECOM_SHIPTO_POSTAL_STREET_LINE2' => '',
            'ECOM_SHIPTO_POSTAL_COUNTRYCODE' => 'DE',
            'ECOM_SHIPTO_POSTAL_CITY' => 'Leipzig',
            'ECI' => 1,
            'CUID' => null,
        );
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testConfirmDdPayment()
    {
        $quote = Mage::getModel('sales/quote')->load(10);
        $order = Mage::getModel('sales/order')->load(11);

        $observerMock = $this->getModelMock('ops/observer', array('performDirectLinkRequest'));

        $requestParams = array(
            'AMOUNT' => 0.0,
            'CARDNO' => '12335BLZ12345566',
            'CN' => utf8_decode('Hubertus zu Fürstenberg'),
            'CURRENCY' => 'USD',
            'ED' => '9999',
            'OPERATION' => 'RES',
            'ORDERID' => Mage::getSingleton('ops/config')->getConfigData('devprefix') . $quote->getId(),
            'PM' => 'Direct Debits DE',
            'OWNERADDRESS' => utf8_decode('An der Tabaksmühle 3a'),
            'OWNERZIP' => '04229',
            'OWNERTELNO' => null,
            'OWNERCTY' => 'DE',
            'ADDMATCH' => 1,
            'ECOM_SHIPTO_POSTAL_POSTALCODE' => '04229',
            'ECOM_BILLTO_POSTAL_POSTALCODE' => '04229',
            'CUID' => null,
            'BRAND' => 'Direct Debits DE',
            'ECI' => Netresearch_OPS_Model_Eci_Values::MANUALLY_KEYED_FROM_MOTO,
            'OWNERTOWN' => 'Leipzig'
        );

        $directDebitHelperMock = $this->getHelperMock('ops/directDebit', array('getDirectLinkRequestParams'));
        $directDebitHelperMock->expects($this->any())
            ->method('getDirectLinkRequestParams')
            ->will($this->returnValue($requestParams));
        $this->replaceByMock('helper', 'ops/directDebit', $directDebitHelperMock);

        $observerMock->expects($this->any())
            ->method('performDirectLinkRequest')
            ->with($quote, $requestParams, 1)
            ->will($this->returnValue('MOTO'));
        $this->assertEquals('MOTO', $observerMock->confirmDdPayment($order, $quote));
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testConfirmDdPaymentWithNoECI()
    {
        $quote = Mage::getModel('sales/quote')->load(10);
        $order = Mage::getModel('sales/order')->load(11);



        $observerMock = $this->getModelMock('ops/observer', array('performDirectLinkRequest'));


        $requestParams = array(
            'AMOUNT' => 0.0,
            'CARDNO' => '12335BLZ12345566',
            'CN' => utf8_decode('Hubertus zu Fürstenberg'),
            'CURRENCY' => 'USD',
            'ED' => '9999',
            'OPERATION' => 'RES',
            'ORDERID' => Mage::getSingleton('ops/config')->getConfigData('devprefix') . $quote->getId(),
            'PM' => 'Direct Debits DE',
            'OWNERADDRESS' => utf8_decode('An der Tabaksmühle 3a'),
            'OWNERZIP' => '04229',
            'OWNERTELNO' => null,
            'OWNERCTY' => 'DE',
            'ADDMATCH' => 1,
            'ECOM_SHIPTO_POSTAL_POSTALCODE' => '04229',
            'ECOM_BILLTO_POSTAL_POSTALCODE' => '04229',
            'CUID' => null,
            'OWNERTOWN' => 'Leipzig',
            'BRAND' => 'Direct Debits DE'
        );

        $directDebitHelperMock = $this->getHelperMock('ops/directDebit', array('getDirectLinkRequestParams'));
        $directDebitHelperMock->expects($this->any())
            ->method('getDirectLinkRequestParams')
            ->will($this->returnValue($requestParams));
        $this->replaceByMock('helper', 'ops/directDebit', $directDebitHelperMock);


        $observerMock->expects($this->any())
            ->method('performDirectLinkRequest')
            ->with($quote, $requestParams, 1)
            ->will($this->returnValue('ECOM'));

        $this->assertEquals('ECOM', $observerMock->confirmDdPayment($order, $quote));
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testGetPaymentAction()
    {
        $order = Mage::getModel('sales/order')->load(11);
        $configMock = $this->getModelMock('ops/config', array('getPaymentAction'));
        $configMock->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue('bla'));
        $this->replaceByMock('model', 'ops/config', $configMock);
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION,
            Mage::getModel('ops/observer')->_getPaymentAction($order)
        );

        $order = Mage::getModel('sales/order')->load(21);
        $configMock = $this->getModelMock('ops/config', array('getPaymentAction'));
        $configMock->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue('authorize_capture'));
        $this->replaceByMock('model', 'ops/config', $configMock);
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_CAPTURE_ACTION,
            Mage::getModel('ops/observer')->_getPaymentAction($order)
        );

        $order = Mage::getModel('sales/order')->load(21);
        $configMock = $this->getModelMock('ops/config', array('getPaymentAction'));
        $configMock->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue('authorize'));
        $this->replaceByMock('model', 'ops/config', $configMock);
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION,
            Mage::getModel('ops/observer')->_getPaymentAction($order)
        );

        $order = Mage::getModel('sales/order')->load(22);
        $configMock = $this->getModelMock('ops/config', array('getPaymentAction'));
        $configMock->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue('authorize_capture'));
        $this->replaceByMock('model', 'ops/config', $configMock);
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_DIRECTDEBIT_NL,
            Mage::getModel('ops/observer')->_getPaymentAction($order)
        );

        $order = Mage::getModel('sales/order')->load(22);
        $configMock = $this->getModelMock('ops/config', array('getPaymentAction'));
        $configMock->expects($this->any())
            ->method('getPaymentAction')
            ->will($this->returnValue('authorize'));
        $this->replaceByMock('model', 'ops/config', $configMock);
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_ACTION,
            Mage::getModel('ops/observer')->_getPaymentAction($order)
        );

        $order = Mage::getModel('sales/order')->load(11);
        $configMock = $this->getModelMock('ops/config', array('getPaymentAction'));
        $configMock->expects($this->any())
            ->method('geTPaymentAction')
            ->will($this->returnValue('authorize_capture'));
        $this->replaceByMock('model', 'ops/config', $configMock);
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_CAPTURE_ACTION,
            Mage::getModel('ops/observer')->_getPaymentAction($order)
        );

        $order = Mage::getModel('sales/order')->load(23);
        $configMock = $this->getModelMock('ops/config', array('getPaymentAction'));
        $configMock->expects($this->any())
            ->method('getPaymentAction')
            ->with(1)
            ->will($this->returnValue('authorize_capture'));
        $this->replaceByMock('model', 'ops/config', $configMock);
        Mage::getModel('ops/observer')->_getPaymentAction($order);
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::OPS_AUTHORIZE_CAPTURE_ACTION,
            Mage::getModel('ops/observer')->_getPaymentAction($order)
        );
    }
}
