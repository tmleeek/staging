<?php

class MDN_Mpm_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function isConfigured()
    {
        return (Mage::getStoreConfig('mpm/account/login') && Mage::getStoreConfig('mpm/account/password'));
    }

    public function log($msg, $logFile = 'mpm.log')
    {
        Mage::log($msg, null, $logFile);
    }

    public function getClientLogPath()
    {
        if (Mage::getStoreConfig('system/log/enable_log') > 0)
            return Mage::getBaseDir('var').DS.'log'.DS.'mpm_client_log.log';
        else
            return null;
    }

    public function getBrandAttribute()
    {
        return Mage::getStoreConfig('mpm/misc/brand_attribute');
    }

    public function getSupplierAttribute()
    {
        return Mage::getStoreConfig('mpm/misc/supplier_attribute');
    }

    /**
     * @return array
     */
    public function getInstalledVersion()
    {
        $version = array();
        $tablePrefix = Mage::getConfig()->getTablePrefix();
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');

        $name = 'Mpm';
        $key = 'MDN_' . $name;

        if (array_key_exists($key, $modules)) {
            $sql = "select version from " . $tablePrefix . "core_resource where code='" . $name . "_setup'";

            $version = array(
                'config_version' => $modules[$key]->version,
                'installed_version' => $read->fetchOne($sql),
                'code_pool' => $modules[$key]->codePool
            );
        }


        return $version;
    }

}
