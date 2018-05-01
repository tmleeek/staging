<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup Upgrade from 1.1.2 to 1.2.0.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */

/** @var $installer Mage_Customer_Model_Entity_Setup */
$installer	= $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$installer->getTable('magebackup_backups')}
	CHANGE COLUMN `file_name` `file_name` TEXT NOT NULL,
  	ADD COLUMN `status` VARCHAR(30) NOT NULL DEFAULT '',
	ADD COLUMN `multipart` INT(11) NOT NULL DEFAULT 0,
	ADD COLUMN `file_path` TEXT NOT NULL,
	ADD COLUMN `remote_path` TEXT NOT NULL,
	ADD COLUMN `total_size` TEXT NOT NULL;
");

$installer->endSetup();