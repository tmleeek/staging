<?php

$installer = $this;
$installer->startSetup();

$installer->attemptQuery("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('rewardssocial/purchase_share')}` (
        `purchase_share_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `customer_id` INT(10) UNSIGNED NOT NULL,
        `order_id` INT(10) UNSIGNED NOT NULL,
        `product_id` INT(10) UNSIGNED NOT NULL,
        `type_id` INT(10) UNSIGNED NOT NULL,
        `created_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(`purchase_share_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->addForeignKey(
    'FK_REWARDSSOCIAL_PURCHASE_SHARE_CUSTOMER',
    $this->getTable('rewardssocial/purchase_share'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);

$installer->addIndex(
    $this->getTable('rewardssocial/purchase_share'),
    array('customer_id', 'order_id', 'type_id'),
    'IDX_PURCHASE_SHARE_CUSTOMER_ID_ORDER_ID_TYPE_ID'
);

$installer->endSetup();
