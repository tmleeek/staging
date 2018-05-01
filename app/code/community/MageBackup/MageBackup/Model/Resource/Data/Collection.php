<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile data collection model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Resource_Data_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
	/**
	 * Constructor.
	 */
	public function _construct() {
		$this->_init('magebackup/data');
	}

	public function setProfileId($id) {
		$this->addFieldToFilter('profile_id', array('eq' => (int) $id));

		return $this;
	}
}