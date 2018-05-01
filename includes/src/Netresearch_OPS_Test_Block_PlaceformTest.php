<?php
class Netresearch_OPS_Test_Block_PlaceformTest extends EcomDev_PHPUnit_Test_Case
{
    public function testGetFormAction()
    {
        $block = Mage::app()->getLayout()->getBlockSingleton('ops/placeform');
        $blockMock = $this->getBlockMock('ops/placeform', array('getQuestion'));
        $blockMock->expects($this->any())
            ->method('getQuestion')
            ->will($this->returnValue('How much is the fish?'));

        $action = $blockMock->getFormAction();
        $this->assertEquals(Mage::getUrl('*/*/*', array('_secure' => Mage::app()->getFrontController()->getRequest()->isSecure())), $action);

        // check explicitly for https
        $_SERVER['HTTPS'] = 'on';
        $action = $blockMock->getFormAction();
        $this->assertEquals(Mage::getUrl('*/*/*', array('_secure' =>true)), $action);

        $blockMock = $this->getBlockMock('ops/placeform', array('getQuestion'));
        $blockMock->expects($this->any())
            ->method('getQuestion')
            ->will($this->returnValue(null));

        $action = $blockMock->getFormAction();
        $this->assertEquals($blockMock->getConfig()->getFrontendGatewayPath(), $action);
    }
    
    public function testIsKwixoPaymentMethodTrue()
    {
        $order = Mage::getModel('sales/order');
        $payment = Mage::getModel('sales/order_payment');
        $payment->setMethod('ops_kwixoApresReception');
        $order->setPayment($payment);
        
        $blockMock = $this->getBlockMock('ops/placeform', array('_getOrder'));
        $blockMock->expects($this->any())
            ->method('_getOrder')
            ->will($this->returnValue($order));
        
        $this->assertTrue($blockMock->isKwixoPaymentMethod());
        
    }
    
    public function testIsKwixoPaymentMethodFalse()
    {
        $order = Mage::getModel('sales/order');
        $payment = Mage::getModel('sales/order_payment');
        $payment->setMethod('ops_cc');
        $order->setPayment($payment);
        
        $blockMock = $this->getBlockMock('ops/placeform', array('_getOrder'));
        $blockMock->expects($this->any())
            ->method('_getOrder')
            ->will($this->returnValue($order));
        
        $this->assertFalse($blockMock->isKwixoPaymentMethod());
        
    }
    
}