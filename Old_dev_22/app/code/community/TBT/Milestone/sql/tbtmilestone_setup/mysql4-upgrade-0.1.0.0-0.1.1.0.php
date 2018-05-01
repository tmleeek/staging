<?php

$this->startSetup();

$this->attemptQuery("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('tbtmilestone/rule_log')}` (
        `log_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `rule_id` INT(10) UNSIGNED NOT NULL,
        `rule_name` VARCHAR(255) DEFAULT NULL,
        `condition_type` VARCHAR(255) DEFAULT NULL,
        `action_type` VARCHAR(255) DEFAULT NULL,
        `customer_id` INT(10) UNSIGNED NOT NULL,
        `executed_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`log_id`),
        CONSTRAINT `FK_CUSTOMER_ID` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable( 'customer_entity' )}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_RULE_ID` FOREIGN KEY (`rule_id`) REFERENCES `{$this->getTable('tbtmilestone/rule')}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Contains rule execution records' AUTO_INCREMENT=1 ;
");

$this->addColumns($this->getTable('tbtmilestone/rule'), "`rewards_special_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT  'For backwards compatibility with rewards_special rules' AFTER  `rule_id`");
$this->addForeignKey('FK_REWARDS_SPECIAL_ID', $this->getTable('tbtmilestone/rule'), "rewards_special_id", $this->getTable('rewards/special'), "rewards_special_id", "CASCADE", "CASCADE");

// clear cache
$this->prepareForDb();

$this->endSetup();

