<?php

$installer = $this;
$installer->startSetup();

$installer->attemptQuery("
    ALTER TABLE `{$this->getTable('rewardssocial/pinterest_pin')}`
    ADD COLUMN `is_processed` TINYINT(1) NULL DEFAULT 0;
");

$installer->addIndex(
    $this->getTable('rewardssocial/pinterest_pin'),
    array('customer_id', 'pinned_url'),
    'IDX_PINTEREST_PIN_CUSTOMER_ID_PINNED_URL'
);

// fail-safe to make sure old pin rewards will not be processed again
$installer->attemptQuery("
    UPDATE `{$this->getTable('rewardssocial/pinterest_pin')}` AS `pins`
    SET `pins`.`is_processed` = 1;
");

// clear cache
$installer->prepareForDb();

$installer->endSetup();
