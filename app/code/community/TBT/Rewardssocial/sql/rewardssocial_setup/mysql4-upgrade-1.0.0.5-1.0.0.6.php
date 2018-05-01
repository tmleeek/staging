<?php

$installer = $this;
$installer->startSetup();

// Create facebook share products table
$installer->attemptQuery("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('rewardssocial/facebook_share')}` (
        `facebook_share_id` smallint(11) unsigned NOT NULL auto_increment,
        `customer_id` int(10) unsigned NOT NULL,
        `product_id` int(10) unsigned NOT NULL,
        `post_id` varchar(64) DEFAULT NULL COMMENT 'Facebook response post_id',
        `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (`facebook_share_id`),
        KEY `IDX_CUSTOMER` (`customer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Facebook Product Shares';
");

// Create foreign key constraint for customer_id
$installer->getConnection()->addConstraint(
    "FK_REWARDSSOCIAL_SHARE_CUSTOMER",
    $this->getTable('rewardssocial/facebook_share'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);


$installer->addIndex(
    $this->getTable('rewardssocial/facebook_share'),
    array('customer_id', 'product_id'),
    'IDX_FB_SHARE_CUSTOMER_ID_PRODUCT_ID'
);

$installer->endSetup();
