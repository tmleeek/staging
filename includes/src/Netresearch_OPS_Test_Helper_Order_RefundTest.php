<?php

class Netresearch_OPS_Test_Helper_Order_RefundTest extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @loadFixture ../../../../var/fixtures/orders.yaml
     */
    public function testGetRefundOperation()
    {
        /* @var $helper Netresearch_OPS_Helper_Order_Refund */
        $helper = Mage::helper('ops/order_refund');
        $payment = new Varien_Object();

        // complete refund should lead to RFS
        $order = Mage::getModel('sales/order')->load(11);
        $payment->setOrder($order);
        $payment->setBaseAmountRefundedOnline(0.00);
        $amount = 119.00;
        $this->assertEquals(Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL, $helper->getRefundOperation($payment, $amount));

        // complete refund should lead to RFS
        $order = Mage::getModel('sales/order')->load(16);
        $payment->setOrder($order);
        $payment->setBaseAmountRefundedOnline(0.00);
        $amount = 19.99;
        $this->assertEquals(Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL, $helper->getRefundOperation($payment, $amount));

        // partial refund should lead to RFD
        $order = Mage::getModel('sales/order')->load(11);
        $payment->setOrder($order);
        $payment->setBaseAmountRefundedOnline(0.00);
        $amount = 100.00;
        $this->assertEquals(Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_PARTIAL, $helper->getRefundOperation($payment, $amount));

        // partial refund + new amount to refund should lead to RFS
        $order = Mage::getModel('sales/order')->load(11);
        $payment->setOrder($order);
        $payment->setBaseAmountRefundedOnline(19.00);
        $amount = 100.00;
        $this->assertEquals(Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL, $helper->getRefundOperation($payment, $amount));

        // partial refund + new amount to refund should lead to RFS
        $order = Mage::getModel('sales/order')->load(16);
        $payment->setOrder($order);
        $payment->setBaseAmountRefundedOnline(17.98);
        $amount = 2.01;
        $this->assertEquals(Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL, $helper->getRefundOperation($payment, $amount));
        
        // partial refund + new amount to refund should lead to RFS
        $order = Mage::getModel('sales/order')->load(16);
        $payment->setOrder($order);
        $payment->setBaseAmountRefundedOnline(17.98);
        $amount = 2.00;
        $this->assertEquals(Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_PARTIAL, $helper->getRefundOperation($payment, $amount));

        // partial refund + new amount to refund should lead to RFS
        $order = Mage::getModel('sales/order')->load(16);
        $payment->setOrder($order);
        $payment->setBaseAmountRefundedOnline(17.98);
        $amount = 2.01;
        $this->assertEquals(Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL, $helper->getRefundOperation($payment, $amount, false));

        // partial refund should lead to RFD if not explictly set by merchant
        $order = Mage::getModel('sales/order')->load(11);
        $payment->setOrder($order);
        $payment->setBaseAmountRefundedOnline(0.00);
        $amount = 100.00;
        $this->assertEquals(Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_PARTIAL, $helper->getRefundOperation($payment, $amount, false));

        // partial refund should lead to RFS since merchant wants to close the transaction
        $order = Mage::getModel('sales/order')->load(11);
        $payment->setOrder($order);
        $payment->setBaseAmountRefundedOnline(0.00);
        $amount = 100.00;
        $this->assertEquals(Netresearch_OPS_Model_Payment_Abstract::OPS_REFUND_FULL, $helper->getRefundOperation($payment, $amount, true));

    }


}
