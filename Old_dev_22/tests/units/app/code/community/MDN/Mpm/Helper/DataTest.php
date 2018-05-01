<?php namespace units\app\code\community\MDN\Mpm\Helper;

class DataTest extends \units\BaseHelper {

    protected $_testClassName = 'MDN_Mpm_Helper_Data';

    public function testIsConfigured(){

        $login = 'login';
        $password = 'password';

        $this->addMageStoreConfig('mpm/account/login', $login);
        $this->addMageStoreConfig('mpm/account/password', $password);

        $result = $this->_instance->isConfigured();
        $this->assertTrue($result);

    }

    public function testLog(){

        $msg = 'log';
        $this->_instance->log($msg);

    }

    public function testGetBrandAttribute(){

        $brandAttribute = 'brand_attribute';
        $this->addMageStoreConfig('mpm/misc/brand_attribute', $brandAttribute);
        $result = $this->_instance->getBrandAttribute();
        $this->assertEquals($brandAttribute, $result);

    }

    public function testGetSupplierAttribute(){

        $supplierAttribute = 'supplier_attribute';
        $this->addMageStoreConfig('mpm/misc/supplier_attribute', $supplierAttribute);
        $result = $this->_instance->getSupplierAttribute();
        $this->assertEquals($supplierAttribute, $result);

    }

}