<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup Install 1.2.0.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */

/** @var $installer Mage_Customer_Model_Entity_Setup */
$installer	= $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$installer->getTable('magebackup_backups')};
CREATE TABLE IF NOT EXISTS {$installer->getTable('magebackup_backups')} (
	`backup_id`		int(11) unsigned NOT NULL auto_increment,
	`profile_id`	int(11) unsigned NOT NULL DEFAULT 0,
	`name`			varchar(255) NOT NULL DEFAULT '',
	`description`	text NOT NULL DEFAULT '',
	`status`		varchar(30) NOT NULL DEFAULT '',
	`file_name`		text NOT NULL DEFAULT '',
	`start_time`	datetime NULL,
	`end_time`		datetime NULL,
	`multipart`		int(11) unsigned NOT NULL DEFAULT 0,
	`file_path`		text NOT NULL DEFAULT '',
	`remote_path`	text NOT NULL DEFAULT '',
	`total_size`	bigint(20) NOT NULL DEFAULT 0,
	PRIMARY KEY (`backup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('magebackup_profiles')};
CREATE TABLE IF NOT EXISTS {$installer->getTable('magebackup_profiles')} (
	`profile_id`	int(11) unsigned NOT NULL auto_increment,
	`name`			varchar(255) NOT NULL DEFAULT '',
	`description`	text NOT NULL DEFAULT '',
	`created_time`	datetime NULL,
	`updated_time`	datetime NULL,
	
	PRIMARY KEY (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('magebackup_profile_data')};
CREATE TABLE IF NOT EXISTS {$installer->getTable('magebackup_profile_data')} (
	`data_id`		int(11) NOT NULL AUTO_INCREMENT,
	`profile_id`	int(11) NOT NULL DEFAULT '0',
	`name`			varchar(255) NOT NULL,
	`value`			text NOT NULL,

	PRIMARY KEY (`data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();