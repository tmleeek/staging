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
class MageBackup_MageBackup_Model_Data extends Mage_Core_Model_Abstract {

	/**
	 * Constructor.
	 */
	public function _construct() {
		parent::_construct();

		$this->_init('magebackup/data');
	}

	public function loadByFields($fields) {
		$this->_getResource()->loadByFields($this, $fields);
		$this->setOrigData();

		return $this;
	}
}