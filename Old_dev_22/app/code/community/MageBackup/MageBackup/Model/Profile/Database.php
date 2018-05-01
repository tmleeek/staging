<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile database model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Profile_Database {

	public function getTablesArray() {
		$read	= Mage::getSingleton('core/resource')->getConnection('core_read');
		$query	= 'SHOW TABLES';
		$items	= $read->fetchCol($query);
		$array	= array();

		foreach ($items as $item) {
			$array[$item]	= $item;
		}

		return $array;
	}

	public function getDatabasesArray() {
		$read	= Mage::getSingleton('core/resource')->getConnection('core_read');
		$query	= 'SHOW DATABASES';
		$items	= $read->fetchCol($query);
		$array	= array();

		foreach ($items as $item) {
			$array[$item]	= $item;
		}

		return $array;
	}
}