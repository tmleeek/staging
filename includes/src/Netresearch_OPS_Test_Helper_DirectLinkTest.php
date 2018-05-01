<?php
class Netresearch_OPS_Test_Helper_DirectLinkTest extends EcomDev_PHPUnit_Test_Case
{
    public function setUp()
    {
        parent::setup();
        $this->_helper = Mage::helper('ops/directlink');
        $transaction = Mage::getModel('sales/order_payment_transaction');
        $transaction->setAdditionalInformation('arrInfo', serialize(array(
            'amount' => '184.90'
        )));
        $transaction->setIsClosed(0);
        $this->_transaction = $transaction;
        $this->_order = Mage::getModel('sales/order');
        $this->_order->setGrandTotal('184.90');
        $this->_order->setBaseGrandTotal('184.90');
    }

    public function testDeleteActions()
    {
        $this->assertFalse($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, array('STATUS'=>Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED)));
        $this->assertFalse($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, array('STATUS'=>Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_WAITING)));
        $this->assertFalse($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, array('STATUS'=>Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_UNCERTAIN)));
        $this->assertFalse($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, array('STATUS'=>Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_REFUSED)));
        $this->assertFalse($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, array('STATUS'=>Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_OK)));
        $this->assertFalse($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, array('STATUS'=>Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_DELETED_PROCESSED_MERCHANT)));
    }

    public function testRefundActions()
    {
        $opsRequest = array(
            'STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_REFUNDED,
            'amount' => '184.90'
        );
        $this->assertFalse($this->_helper->isValidOpsRequest(null, $this->_order, $opsRequest), 'Refund should not be possible without open transactions');
        $this->assertTrue($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, $opsRequest), 'Refund should be possible with open transactions');
        $opsRequest['amount'] = '14.90';
        $this->assertFalse($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, $opsRequest), 'Refund should NOT be possible because of differing amount');
    }

    public function testCancelActions()
    {
        $opsRequest = array(
            'STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_VOIDED,
            'amount' => '184.90'
        );
        $this->assertFalse($this->_helper->isValidOpsRequest(null, $this->_order, $opsRequest), 'Cancel should not be possible without open transactions');
        $this->assertTrue($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, $opsRequest), 'Cancel should be possible with open transactions');
        $opsRequest['amount'] = '14.90';
        $this->assertFalse($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, $opsRequest), 'Cancel should NOT be possible because of differing amount');
    }

    public function testCaptureActions()
    {
        $opsRequest = array(
            'STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED,
            'amount' => '184.90'
        );
        $this->assertTrue($this->_helper->isValidOpsRequest(null, $this->_order, $opsRequest), 'Capture should be possible because of no open transactions and matching amount');
        $opsRequest['amount'] = '14.90';
        $this->assertFalse($this->_helper->isValidOpsRequest($this->_transaction, $this->_order, $opsRequest), 'Capture should NOT be possible because of differing amount');
    }

    public function testCleanupParameters()
    {
        $expected = 123.45;
        $result = $this->_helper->formatAmount('123.45');
        $this->assertEquals($expected, $result);

        $result = $this->_helper->formatAmount('\'123.45\'');
        $this->assertEquals($expected, $result);

        $result = $this->_helper->formatAmount('"123.45"');
        $this->assertEquals($expected, $result);

        $expected = $this->_helper->formatAmount(0.3);
        $result = $this->_helper->formatAmount(0.1 + 0.2);
        $this->assertEquals($expected . '', $result . '');
        $this->assertEquals((float) $expected, (float) $result);
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testProcessFeedbackCaptureSuccess()
    {

        $order = Mage::getModel('sales/order')->load(11);
        $directlinkHelperMock = $this->getHelperMock('ops/directlink', array('isValidOpsRequest'));
        $directlinkHelperMock->expects($this->any())
            ->method('isValidOpsRequest')
            ->will($this->returnValue(true));
        $paymentHelperMock = Mage::helper('ops/payment', array('saveOpsStatusToPayment'));


        $closure = function ($order, $params = array()) {
            $order->getPayment()->setAdditionalInformation('status', 666);
            return $order->getPayment();
        };

        $captureHelper = $this->getHelperMock('ops/order_capture', array('acceptCapture'));
        $captureHelper->expects($this->any())
            ->method('acceptCapture')
            ->will($this->returnCallback($closure));
        $this->replaceByMock('helper', 'ops/order_capture', $captureHelper);
        $params = array('STATUS' => Netresearch_OPS_Model_Payment_Abstract::OPS_PAYMENT_REQUESTED);
        $directlinkHelperMock->processFeedback($order, $params);
        $this->assertEquals(666, $order->getPayment()->getAdditionalInformation('status'));
    }
}

