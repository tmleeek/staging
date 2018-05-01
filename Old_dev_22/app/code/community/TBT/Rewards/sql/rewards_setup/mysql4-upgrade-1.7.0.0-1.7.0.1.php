<?php
$installer = $this;

$installer->startSetup();


Mage::helper('rewards/mysql4_install')->attemptQuery($installer, "
    ALTER TABLE `{$this->getTable('rewards/transfer')}`
        ADD COLUMN `is_dev_mode` TINYINT(1) NOT NULL DEFAULT '0';
");


// Clear cache.
Mage::helper( 'rewards/mysql4_install' )->prepareForDb();


$installer->endSetup(); 
