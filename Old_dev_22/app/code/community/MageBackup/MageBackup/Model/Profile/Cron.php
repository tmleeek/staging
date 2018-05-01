<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile cron model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Profile_Cron {
	const CRON_DAILY	= 1;
	const CRON_WEEKLY	= 2;
	const CRON_MONTHLY	= 3;

	public function getCronFrequenciesArray() {
		return array(
			self::CRON_DAILY	=> Mage::helper('magebackup')->__('Daily'),
			self::CRON_WEEKLY	=> Mage::helper('magebackup')->__('Weekly'),
			self::CRON_MONTHLY	=> Mage::helper('magebackup')->__('Monthly'),
		);
	}

	public function getCronHoursArray() {
		$hours	= array();

		for ($i = 0; $i < 24; $i++) {
			$hours[$i]	= sprintf('%02d', $i);
		}

		return $hours;
	}
}