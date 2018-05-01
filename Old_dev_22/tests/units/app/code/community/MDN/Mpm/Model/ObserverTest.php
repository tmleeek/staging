<?php namespace units\app\code\community\MDN\Mpm\Model;

use units\BaseHelper;

class ObserverTest extends BaseHelper {

    /**
     * @var \MDN_Mpm_Model_Observer
     */
    protected $_instance;

    protected $_testClassName = 'MDN_Mpm_Model_Observer';

    public function testExportCatalogNotEnable(){

        $cronEnable = 0;
        $this->addMageStoreConfig('mpm/catalog_export/enable_cron', $cronEnable);
        $this->_instance->exportCatalog();

    }

    public function testExportCatalogEnable(){

        $cronEnable = 1;
        $this->addMageStoreConfig('mpm/catalog_export/enable_cron', $cronEnable);

        $exportHelperMock = $this->buildMock('MDN_Mpm_Helper_Export');
        $exportHelperMock->expects($this->once())
            ->method('ExportCatalog');

        $this->addMageHelper('Mpm/Export', $exportHelperMock);

        $this->_instance->exportCatalog();

    }

    public function testImportPricingNotEnable(){

        $repricingEnable = 0;
        $this->addMageStoreConfig('mpm/repricing/enable', $repricingEnable);
        $this->_instance->importPricing();

    }

    public function testImportPricingEnableWithTestMode(){

        $repricingEnable = 1;
        $testMode = 1;
        $this->addMageStoreConfig('mpm/repricing/enable', $repricingEnable);
        $this->addMageStoreConfig('mpm/repricing/test_mode', $testMode);
        $this->_instance->importPricing();

    }

    public function testImportPricingEnableWithoutTestMode(){

        $repricingEnable = 1;
        $testMode = 0;
        $this->addMageStoreConfig('mpm/repricing/enable', $repricingEnable);
        $this->addMageStoreConfig('mpm/repricing/test_mode', $testMode);

        $pricingImportMock = $this->buildMock('MDN_Mpm_Helper_PricingImport');
        $pricingImportMock->expects($this->once())
            ->method('importAll');

        $this->addMageHelper('Mpm/PricingImport', $pricingImportMock);

        $this->_instance->importPricing();

    }

    public function testConfigurationChangedForAmazon(){

        $merchantId = 'merchantId';
        $accesskey = 'accessKey';
        $secretKey = 'secretKey';
        $channelCode = 'amazon_fr_default';
        $channels = array($channelCode => 'Amazon.fr');
        $mpmConfigurationMock = $this->buildMock('MDN_Mpm_Helper_Configuration');
        $mpmConfigurationMock->expects($this->once())
            ->method('getSubscribedChannelsWithApi')
            ->will($this->returnValue($channels));
        $this->addMageHelper('Mpm/Configuration', $mpmConfigurationMock);
        $this->addMageStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_merchant_id', $merchantId);
        $this->addMageStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_access_key', $accesskey);
        $this->addMageStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_secret_key', $secretKey);
        $data = array(
            'MERCHANT_ID' => $merchantId,
            'AWS_ACCESS_KEY_ID' => $accesskey,
            'AWS_SECRET_ACCESS_KEY' => $secretKey
        );
        $carlMock = $this->buildMock('MDN_Mpm_Helper_Carl');
        $carlMock->expects($this->once())
            ->method('setWebserviceCredentials')
            ->with($channelCode, $data);
        $this->addMageHelper('Mpm/Carl', $carlMock);

        $this->setSellerReferences($carlMock);
        $this->postMapping($carlMock);

        $singleton = $this->buildMock('Mage_Core_Model_Session');
        $singleton->expects($this->once())
            ->method('setData')
            ->with('carl_token', null);
        $this->addMageSingleton('core/session', $singleton);

        $observerMock = $this->buildMock('Varien_Event_Observer');
        $this->_instance->configuration_changed($observerMock);

    }

    public function testConfigurationChangedForFnac(){

        $partnerId = 'partnerId';
        $shopId = 'shopId';
        $key = 'key';
        $channelCode = 'fnac_fr_default';
        $channels = array($channelCode => 'Fnac.fr');
        $mpmConfigurationMock = $this->buildMock('MDN_Mpm_Helper_Configuration');
        $mpmConfigurationMock->expects($this->once())
            ->method('getSubscribedChannelsWithApi')
            ->will($this->returnValue($channels));
        $this->addMageHelper('Mpm/Configuration', $mpmConfigurationMock);
        $this->addMageStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_partner_id', $partnerId);
        $this->addMageStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_shop_id', $shopId);
        $this->addMageStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_key', $key);
        $data = array(
            'PARTNER_ID' => $partnerId,
            'SHOP_ID' => $shopId,
            'KEY' => $key
        );
        $carlMock = $this->buildMock('MDN_Mpm_Helper_Carl');
        $carlMock->expects($this->once())
            ->method('setWebserviceCredentials')
            ->with($channelCode, $data);
        $this->addMageHelper('Mpm/Carl', $carlMock);

        $this->setSellerReferences($carlMock);
        $this->postMapping($carlMock);

        $singleton = $this->buildMock('Mage_Core_Model_Session');
        $singleton->expects($this->once())
            ->method('setData')
            ->with('carl_token', null);
        $this->addMageSingleton('core/session', $singleton);

        $observerMock = $this->buildMock('Varien_Event_Observer');
        $this->_instance->configuration_changed($observerMock);

    }

    public function testConfigurationChangedForOther(){

        $login = 'login';
        $password = 'password';
        $channelCode = 'cdiscount_fr_default';
        $channels = array($channelCode => 'Cdiscount.fr');
        $mpmConfigurationMock = $this->buildMock('MDN_Mpm_Helper_Configuration');
        $mpmConfigurationMock->expects($this->once())
            ->method('getSubscribedChannelsWithApi')
            ->will($this->returnValue($channels));
        $this->addMageHelper('Mpm/Configuration', $mpmConfigurationMock);
        $this->addMageStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_login', $login);
        $this->addMageStoreConfig('mpm/repricing/seller_webservice_' . $channelCode . '_password', $password);
        $data = array(
            'LOGIN' => $login,
            'PASSWORD' => $password,
        );
        $carlMock = $this->buildMock('MDN_Mpm_Helper_Carl');
        $carlMock->expects($this->once())
            ->method('setWebserviceCredentials')
            ->with($channelCode, $data);
        $this->addMageHelper('Mpm/Carl', $carlMock);

        $this->setSellerReferences($carlMock);
        $this->postMapping($carlMock);

        $singleton = $this->buildMock('Mage_Core_Model_Session');
        $singleton->expects($this->once())
            ->method('setData')
            ->with('carl_token', null);
        $this->addMageSingleton('core/session', $singleton);

        $observerMock = $this->buildMock('Varien_Event_Observer');
        $this->_instance->configuration_changed($observerMock);

    }

    protected function setSellerReferences(\PHPUnit_Framework_MockObject_MockObject $carlMock){

        $carlMock->expects($this->once())
            ->method('getChannelsSubscribed')
            ->will($this->returnValue(array()));

    }

    protected function postMapping(\PHPUnit_Framework_MockObject_MockObject $carlMock){

        $carlMock->expects($this->once())
            ->method('getFieldsToMap')
            ->will($this->returnValue(array()));

    }

}