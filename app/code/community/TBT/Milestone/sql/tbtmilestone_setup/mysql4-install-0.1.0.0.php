<?php

$this->startSetup();

// TODO: Consider putting an index on the condition_type column.
$this->attemptQuery("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('tbtmilestone/rule')}` (
        `rule_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `condition_type` VARCHAR(255) NOT NULL,
        `condition_details_json` VARCHAR(1023) NOT NULL,
        `action_type` VARCHAR(255) NOT NULL,
        `action_details_json` VARCHAR(1023) NOT NULL,
        `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
        `website_ids` VARCHAR(255) NULL DEFAULT NULL,
        `customer_group_ids` VARCHAR(255) NULL DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL,
        PRIMARY KEY (`rule_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

// clear cache
$this->prepareForDb();

$this->endSetup();
