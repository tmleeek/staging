<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile data model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Status {
	const STATUS_YES		= 1;
	const STATUS_NO			= 0;

	public static function getYesNoArray() {
		return array(
			self::STATUS_YES	=> Mage::helper('magebackup')->__('Yes'),
			self::STATUS_NO		=> Mage::helper('magebackup')->__('No'),
		);
	}
}