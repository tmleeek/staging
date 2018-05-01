<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile backup type model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Profile_Backuptype {
	const TYPE_ALL		= 0;
	const TYPE_FILE		= 1;
	const TYPE_DATABASE	= 2;

	public static function getBackupTypesArray() {
		$options	= array(
			self::TYPE_ALL		=> Mage::helper('magebackup')->__('Files and Database'),
			self::TYPE_FILE		=> Mage::helper('magebackup')->__('Files only'),
			self::TYPE_DATABASE	=> Mage::helper('magebackup')->__('Database only'),
		);

		return $options;
	}
}