<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_MigrationToMagento2Controller
    extends Ess_M2ePro_Controller_Adminhtml_BaseController
{
    //########################################

    public function disableModuleAction()
    {
        $this->initialize();

        try {
            $this->prepareDatabase();
        } catch (Exception $exception) {
            $this->getSession()->addError(
                Mage::helper('M2ePro')->__(
                    'M2E Pro was not disabled. Reason: %error_message%.', $exception->getMessage()
                )
            );

            return $this->_redirect('adminhtml/dashboard');
        }

        $this->finish();

        $this->getSession()->addSuccess(
            Mage::helper('M2ePro')->__('M2E Pro was successfully disabled.')
        );
        return $this->_redirect('adminhtml/dashboard');
    }

    //########################################

    private function initialize()
    {
        Mage::helper('M2ePro/Primary')->getConfig()->setGroupValue(
            '/migrationtomagento2/source/', 'is_prepared_for_migration', 0
        );
        Mage::helper('M2ePro/Primary')->getConfig()->setGroupValue(
            '/migrationtomagento2/source/magento/', 'version', Mage::helper('M2ePro/Magento')->getVersion()
        );
        Mage::helper('M2ePro/Primary')->getConfig()->setGroupValue(
            '/migrationtomagento2/source/magento/', 'edition', Mage::helper('M2ePro/Magento')->getEditionName()
        );
        Mage::helper('M2ePro/Primary')->getConfig()->setGroupValue(
            '/migrationtomagento2/source/magento/', 'tables_prefix',
            Mage::helper('M2ePro/Magento')->getDatabaseTablesPrefix()
        );
        Mage::helper('M2ePro/Primary')->getConfig()->setGroupValue(
            '/migrationtomagento2/source/m2epro/', 'version', Mage::helper('M2ePro/Module')->getVersion()
        );
    }

    private function prepareDatabase()
    {
        $resource   = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $tablesForClearing = array(
            'm2epro_ebay_dictionary_category',
            'm2epro_ebay_dictionary_marketplace',
            'm2epro_ebay_dictionary_shipping',
            'm2epro_ebay_dictionary_motor_epid',
            'm2epro_ebay_dictionary_motor_ktype',
            'm2epro_amazon_dictionary_category',
            'm2epro_amazon_dictionary_category_product_data',
            'm2epro_amazon_dictionary_marketplace',
            'm2epro_amazon_dictionary_specific',
            'm2epro_amazon_dictionary_shipping_override',
            'm2epro_operation_history',
            'm2epro_cache_config',
        );

        foreach ($tablesForClearing as $tableName) {
            $connection->truncateTable($resource->getTableName($tableName));
        }

        $connection = $resource->getConnection('core_setup');

        $tablesForRemove = array(
            $resource->getTableName('m2epro_migration_v6'),
        );

        $tablesPrefixesForRemove = array(
            'm2epro__backup_v5',
            'ess__backup_v5',
            'm2epro__source',
            'ess__source',
            'm2epro__backup_v611',
            'm2epro__backup_v630',
        );

        foreach ($tablesPrefixesForRemove as $prefix) {
            $prefixedTables = $connection->query('SHOW TABLES LIKE \'%'.$prefix.'%\'')->fetchAll(Zend_Db::FETCH_COLUMN);
            $tablesForRemove = array_merge($tablesForRemove, $prefixedTables);
        }

        foreach ($tablesForRemove as $tableName) {
            $connection->dropTable($tableName);
        }
    }

    private function finish()
    {
        Mage::helper('M2ePro/Primary')->getConfig()->setGroupValue(
            '/migrationToMagento2/source/', 'is_prepared_for_migration', 1
        );
        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue(NULL, 'is_disabled', 1);
        Mage::helper('M2ePro/Magento')->clearCache();
    }

    //########################################
}