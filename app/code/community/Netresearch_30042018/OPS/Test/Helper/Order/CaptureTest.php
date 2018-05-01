<?php
class Netresearch_OPS_Test_Helper_Order_CaptureTest extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @loadFixture ../../../../var/fixtures/orders.yaml
     */
    public function testDetermineIsPartial()
    {
        $helper = Mage::helper('ops/order_capture');
        $payment = new Varien_Object();
        
        //Complete
        $order = Mage::getModel('sales/order')->load(11);
        $payment->setOrder($order);
        $payment->setBaseAmountPaidOnline(0.00);
        $amount = 119.00;
        $this->assertFalse($helper->determineIsPartial($payment, $amount));
        
        //Partial
        $order = Mage::getModel('sales/order')->load(11);
        $payment->setOrder($order);
        $payment->setBaseAmountPaidOnline(0.00);
        $amount = 100.00;
        $this->assertTrue($helper->determineIsPartial($payment, $amount));
        
        //Partial
        $order = Mage::getModel('sales/order')->load(11);
        $payment->setOrder($order);
        $payment->setBaseAmountPaidOnline(18.99);
        $amount = 100.00;
        $this->assertTrue($helper->determineIsPartial($payment, $amount));
        
        //Complete
        $order = Mage::getModel('sales/order')->load(11);
        $payment->setOrder($order);
        $payment->setBaseAmountPaidOnline(19.00);
        $amount = 100.00;
        $this->assertFalse($helper->determineIsPartial($payment, $amount));
    }

    public function testPrepareOperationPartial()
    {
        $invoice = array("items" => "foo");
        Mage::app()->getRequest()->setParam('invoice', $invoice);
        $payment = new Varien_Object();
        
        $helperMock = $this->getHelperMock('ops/order_capture', array('determineIsPartial'));
        $helperMock->expects($this->any())
            ->method('determineIsPartial')
            ->will($this->returnValue(true));
        $this->replaceByMock('helper', 'ops/order_capture', $helperMock);
        $helper = Mage::helper('ops/order_capture');
        $expected = array(
            "items" => $invoice["items"],
            "operation" => Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_PARTIAL,
            "type" => "partial",
            "amount" => 0
        );
        $this->assertEquals($expected, $helper->prepareOperation($payment, 0));
    }
    
    public function testPrepareOperationFull()
    {
        $invoice = array("items" => "foo");
        Mage::app()->getRequest()->setParam('invoice', $invoice);
        $payment = new Varien_Object();
        
        $helperMock = $this->getHelperMock('ops/order_capture', array('determineIsPartial'));
        $helperMock->expects($this->any())
            ->method('determineIsPartial')
            ->will($this->returnValue(false));
        $this->replaceByMock('helper', 'ops/order_capture', $helperMock);
        $helper = Mage::helper('ops/order_capture');
        $expected = array(
            "items" => $invoice["items"],
            "operation" => Netresearch_OPS_Model_Payment_Abstract::OPS_CAPTURE_FULL,
            "type" => "full",
            "amount" => 0
        );
        $this->assertEquals($expected, $helper->prepareOperation($payment, 0));
    }
}