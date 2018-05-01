<?php
class Netresearch_OPS_Test_Helper_OrderTest extends EcomDev_PHPUnit_Test_Case
{

    private $devPrefix = '';

    public function setUp()
    {
        $this->devPrefix = 'DEV';
        parent::setUp();
    }


    /**
     * @test
     */
    public function testGetOpsOrderIdFromOrderWithOrderIdAsOrderReference()
    {
        $orderRef = '123';
        $configMock = $this->getModelMock('ops/config', array('getConfigData','getOrderReference'));
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue($this->devPrefix));

        $configMock->expects($this->any())
            ->method('getOrderReference')
            ->will($this->returnValue('orderId'));
        $this->replaceByMock('model', 'ops/config', $configMock);

        $salesObject = Mage::getModel('sales/order')->setIncrementId($orderRef);

        /** @var Netresearch_OPS_Helper_Order $helper */
        $helper = Mage::helper('ops/order');

        $result = $helper->getOpsOrderId($salesObject);
        $this->assertEquals($this->devPrefix . '#' . $orderRef, $result);
    }


    /**
     * @test
     */
    public function testGetOpsOrderIdFromQuoteWithOrderIdAsOrderReference()
    {
        $orderRef = '123';
        $configMock = $this->getModelMock('ops/config', array('getConfigData','getOrderReference'));
        $configMock->expects($this->any())
            ->method('getConfigData')
            ->with('devprefix')
            ->will($this->returnValue($this->devPrefix));

        $configMock->expects($this->any())
            ->method('getOrderReference')
            ->will($this->returnValue('orderId'));
        $this->replaceByMock('model', 'ops/config', $configMock);

        $salesObject = $this->getModelMock(
            'sales/quote',
            array('getStoreId', 'reserveOrderId', 'getReservedOrderId'),
            false,
            array(),
            '',
            false
        );

        $salesObject
            ->expects($this->once())
            ->method('getReservedOrderId')
            ->will($this->returnValue($orderRef));

        /** @var Netresearch_OPS_Helper_Order $helper */
        $helper = Mage::helper('ops/order');

        $result = $helper->getOpsOrderId($salesObject);
        $this->assertEquals($this->devPrefix . '#' . $orderRef, $result);
    }

    /**
     * @loadFixture order.yaml
     */
    public function testGetQuote()
    {
        $order = Mage::getModel('sales/order')->load(1);
        $quote = Mage::helper('ops/order')->getQuote($order->getQuoteId());
        $this->assertTrue($quote instanceof Mage_Sales_Model_Quote);
        $this->assertEquals(1, $quote->getId());
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testCheckIfAddressAreSameWithSameAddressData()
    {
        $order = Mage::getModel('sales/order')->load(11);
        $this->assertTrue(
            (bool)Mage::helper('ops/order')->checkIfAddressesAreSame($order)
        );

        $order = Mage::getModel('sales/order')->load(27);
        $this->assertFalse(
            (bool)Mage::helper('ops/order')->checkIfAddressesAreSame($order)
        );
    }

    /**
     * @loadFixture ../../../var/fixtures/orders.yaml
     */
    public function testCheckIfAddressAreSameWithDifferentAddressData()
    {
        $order = Mage::getModel('sales/order')->load(12);
        $this->assertFalse(
            (bool)Mage::helper('ops/order')->checkIfAddressesAreSame($order)
        );
    }

    public function testSetDataHelper()
    {
        $dataHelper = $this->getHelperMock('ops/data');
        $helper = Mage::helper('ops/order');
        $helper->setDataHelper($dataHelper);
        $this->assertEquals($dataHelper, $helper->getDataHelper());
    }
}
