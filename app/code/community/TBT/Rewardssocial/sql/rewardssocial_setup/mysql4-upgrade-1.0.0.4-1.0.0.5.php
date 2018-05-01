<?php

$installer = $this;
$installer->startSetup();

$installer->attemptQuery("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('rewardssocial/customer')}` (
        `customer_id` int(10) unsigned NOT NULL,
        `is_following` TINYINT(1) NULL DEFAULT NULL,
        `pinterest_username` VARCHAR(64) NULL DEFAULT NULL,
        PRIMARY KEY  (`customer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    "FK_REWARDSSOCIAL_CUSTOMER",
    $this->getTable('rewardssocial/customer'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);

$installer->attemptQuery("
    ALTER TABLE `{$this->getTable('rewardssocial/customer')}`
    ADD COLUMN `pinterest_username` VARCHAR(64) NULL DEFAULT NULL;
");


$installer->attemptQuery("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('rewardssocial/pinterest_pin')}` (
        `pinterest_pin_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `customer_id` INT(10) UNSIGNED NOT NULL,
        `pinterest_url` VARCHAR(256) NOT NULL,
        `pinned_url` VARCHAR(256) NOT NULL,
        PRIMARY KEY(`pinterest_pin_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->attemptQuery("
    ALTER TABLE `{$this->getTable('rewardssocial/pinterest_pin')}`
    ADD COLUMN `created_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
");

$installer->getConnection()->addConstraint(
    "FK_REWARDSSOCIAL_PINTEREST_PIN_CUSTOMER",
    $this->getTable('rewardssocial/pinterest_pin'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);


$installer->attemptQuery("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('rewardssocial/google_plusone')}` (
        `google_plusone_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `customer_id` INT(10) UNSIGNED NOT NULL,
        `url` VARCHAR(256) NOT NULL,
        `created_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(`google_plusone_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addConstraint(
    "FK_REWARDSSOCIAL_GOOGLE_PLUSONE_CUSTOMER",
    $this->getTable('rewardssocial/google_plusone'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);


$installer->attemptQuery("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('rewardssocial/referral_share')}` (
        `referral_share_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `customer_id` INT(10) UNSIGNED NOT NULL,
        `created_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`referral_share_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

// Create foreign key constraint for customer_id
$installer->getConnection()->addConstraint(
    "FK_REWARDSSOCIAL_REFERRAL_SHARE_CUSTOMER",
    $this->getTable('rewardssocial/referral_share'),
    'customer_id',
    $this->getTable('customer/entity'),
    'entity_id'
);

$installer->endSetup();
