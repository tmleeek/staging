<?php
class Netresearch_OPS_Test_Model_ConfigTest
    extends EcomDev_PHPUnit_Test_Case_Config
{
    private $_model;

    public function setUp()
    {
        parent::setup();
        $this->_model = Mage::getModel('ops/config');
    }

    public function testType()
    {
        $this->assertInstanceOf('Netresearch_OPS_Model_Config', $this->_model);
    }

    public function testGetIntersolveBrands()
    {
        $this->assertTrue(is_array($this->_model->getIntersolveBrands(null)));
        $this->assertEquals(
            0, sizeof($this->_model->getIntersolveBrands(null))
        );

        $path = 'payment/ops_interSolve/brands';

        $newVouchers = array(
            array('brand' => '1234', 'value' => '1234'),
            array('brand' => '5678', 'value' => '5678'),
            array('brand' => '9012', 'value' => '9012'),
        );

        $store = Mage::app()->getStore(0)->load(0);
        $store->setConfig($path, serialize($newVouchers));
        $this->assertEquals(
            sizeof($newVouchers),
            sizeof($this->_model->getIntersolveBrands(null))
        );
    }

    public function testGetInlinePaymentCcTypes()
    {
        $sourceModel = Mage::getModel(
            'ops/source_cc_aliasInterfaceEnabledTypes'
        );

        $pathRedirectAll = 'payment/ops_cc/redirect_all';
        $pathSpecific = 'payment/ops_cc/inline_types';
        $store = Mage::app()->getStore(0)->load(0);

        $store->resetConfig();
        $store->setConfig($pathRedirectAll, 0);
        $store->setConfig($pathSpecific, 'MasterCard,VISA');
        $this->assertEquals(
            array('MasterCard', 'VISA'),
            $this->_model->getInlinePaymentCcTypes()
        );

        $store->resetConfig();
        $store->setConfig($pathRedirectAll, 1);
        $store->setConfig($pathSpecific, 'MasterCard,VISA');
        $this->assertEquals(array(), $this->_model->getInlinePaymentCcTypes());

        $store->resetConfig();
    }

    public function testGetGenerateHashUrl()
    {
        $urlModel = $this->getModelMock('core/url', array('getUrl'));
        $urlModel->expects($this->any())
            ->method('getUrl')
            ->with(
                'ops/payment/generatehash',
                array('_secure' => false, '_nosid' => true)
            );
        $this->replaceByMock('model', 'core/url', $urlModel);
        $this->_model->getGenerateHashUrl();

        $urlModel = $this->getModelMock('core/url', array('getUrl'));
        $urlModel->expects($this->any())
            ->method('getUrl')
            ->with(
                'ops/payment/generatehash',
                array('_secure' => false, '_nosid' => true, '_store' => 1)
            );
        $this->replaceByMock('model', 'core/url', $urlModel);
        $this->_model->getGenerateHashUrl(1);
    }

    public function testGetAliasAcceptUrl()
    {
        $urlModel = $this->getModelMock('core/url', array('getUrl'));
        $urlModel->expects($this->any())
            ->method('getUrl')
            ->with(
                'ops/payment/acceptAlias',
                array('_secure' => false, '_nosid' => true)
            );
        $this->replaceByMock('model', 'core/url', $urlModel);
        $this->_model->getAliasAcceptUrl();

        $urlModel = $this->getModelMock('core/url', array('getUrl'));
        $urlModel->expects($this->any())
            ->method('getUrl')
            ->with(
                'ops/payment/acceptAlias',
                array('_secure' => false, '_nosid' => true, '_store' => 1)
            );
        $this->replaceByMock('model', 'core/url', $urlModel);
        $this->_model->getAliasAcceptUrl(1);
    }

    public function testGetAliasExceptionUrl()
    {
        $urlModel = $this->getModelMock('core/url', array('getUrl'));
        $urlModel->expects($this->any())
            ->method('getUrl')
            ->with(
                'ops/payment/exceptionAlias',
                array('_secure' => false, '_nosid' => true)
            );
        $this->replaceByMock('model', 'core/url', $urlModel);
        $this->_model->getAliasExceptionUrl();

        $urlModel = $this->getModelMock('core/url', array('getUrl'));
        $urlModel->expects($this->any())
            ->method('getUrl')
            ->with(
                'ops/payment/exceptionAlias',
                array('_secure' => false, '_nosid' => true, '_store' => 1)
            );
        $this->replaceByMock('model', 'core/url', $urlModel);
        $this->_model->getAliasExceptionUrl(1);
    }

    public function testGetCcSaveAliasUrl()
    {
        $urlModel = $this->getModelMock('core/url', array('getUrl'));
        $urlModel->expects($this->any())
            ->method('getUrl')
            ->with('ops/payment/saveAlias', array('_secure' => false));
        $this->replaceByMock('model', 'core/url', $urlModel);
        $this->_model->getCcSaveAliasUrl();

        $urlModel = $this->getModelMock('core/url', array('getUrl'));
        $urlModel->expects($this->any())
            ->method('getUrl')
            ->with(
                'ops/payment/saveAlias',
                array('_secure' => false, '_store' => 1)
            );
        $this->replaceByMock('model', 'core/url', $urlModel);
        $this->_model->getCcSaveAliasUrl(1);
    }

    public function testIsAliasInfoBlockEnabled()
    {
        $path = 'payment/ops_cc/show_alias_manager_info_for_guests';
        $store = Mage::app()->getStore(0)->load(0);
        $store->resetConfig();
        $store->setConfig($path, 0);
        $this->assertFalse($this->_model->isAliasInfoBlockEnabled());

        $store->resetConfig();
        $store->setConfig($path, 1);
        $this->assertTrue($this->_model->isAliasInfoBlockEnabled());
    }


    public function testObserveCreditMemoCreation()
    {
        $this->assertEventObserverDefined(
            'adminhtml', 'core_block_abstract_to_html_after', 'ops/observer',
            'showWarningForClosedTransactions'
        );
    }

    public function testAppendCheckboxToRefundForm()
    {
        $this->assertEventObserverDefined(
            'adminhtml', 'core_block_abstract_to_html_after', 'ops/observer',
            'appendCheckBoxToRefundForm'
        );
    }

    public function testGetOrderReference()
    {
        $store = Mage::app()->getStore(0)->load(0);
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::REFERENCE_QUOTE_ID,
            $this->_model->getOrderReference()
        );

        $store->setConfig(
            'payment_services/ops/redirectOrderReference',
            Netresearch_OPS_Model_Payment_Abstract::REFERENCE_ORDER_ID
        );
        $this->assertEquals(
            Netresearch_OPS_Model_Payment_Abstract::REFERENCE_ORDER_ID,
            $this->_model->getOrderReference()
        );
    }

    public function testGetShowQuoteIdInOrderGrid()
    {
        $store = Mage::app()->getStore(0)->load(0);
        $this->assertEquals(1, $this->_model->getShowQuoteIdInOrderGrid());

        $store->setConfig('payment_services/ops/showQuoteIdInOrderGrid', 0);
        $this->assertEquals(0, $this->_model->getShowQuoteIdInOrderGrid());
    }


    public function testIsTrackingCodeActivated()
    {
        $store = Mage::app()->getStore(0)->load(0);
        $this->assertFalse($this->_model->isTrackingCodeActivated());

        $store->setConfig('payment_services/ops/enableTrackingCode', 1);
        $this->assertTrue($this->_model->isTrackingCodeActivated());
    }

    
    public function testIsAliasManagerEnabled()
    {
        $path = 'payment/ops_cc/active_alias';
        $store = Mage::app()->getStore(0)->load(0);
        $store->resetConfig();
        $store->setConfig($path, 0);
        $this->assertFalse($this->_model->isAliasManagerEnabled());

        $store->resetConfig();
        $store->setConfig($path, 1);
        $this->assertTrue($this->_model->isAliasManagerEnabled());
        
    }


    public function testGetAcceptRedirectLocation()
    {
        $this->assertEquals(
            Netresearch_OPS_Model_Config::OPS_CONTROLLER_ROUTE_PAYMENT
            . 'accept', $this->_model->getAcceptRedirectRoute()
        );
    }

    public function testGetCancelRedirectLocation()
    {
        $this->assertEquals(
            Netresearch_OPS_Model_Config::OPS_CONTROLLER_ROUTE_PAYMENT
            . 'cancel', $this->_model->getCancelRedirectRoute()
        );
    }

    public function testGetDeclineRedirectLocation()
    {
        $this->assertEquals(
            Netresearch_OPS_Model_Config::OPS_CONTROLLER_ROUTE_PAYMENT
            . 'decline', $this->_model->getDeclineRedirectRoute()
        );
    }

    public function testGetExceptionRedirectLocation()
    {
        $this->assertEquals(
            Netresearch_OPS_Model_Config::OPS_CONTROLLER_ROUTE_PAYMENT
            . 'exception', $this->_model->getExceptionRedirectRoute()
        );
    }


}

