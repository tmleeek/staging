<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile data entity resource model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Resource_Data extends Mage_Core_Model_Resource_Db_Abstract {

	/**
	 * Constructor.
	 */
	public function _construct() {
		$this->_init('magebackup/data', 'data_id');
	}

	public function loadByFields(Mage_Core_Model_Abstract $object, $fields) {
		$read	= $this->_getReadAdapter();

		if (!$read || is_null($fields) || !count($fields)) {
			return;
		}

		$select	= $read->select()
			->from($this->getMainTable())
		;

		foreach ($fields as $key => $value) {
			$field	= $read->quoteIdentifier(sprintf('%s.%s', $this->getMainTable(), $key));
			$select->where($field . '=?', $value);
		}

		$id	= $read->fetchOne($select);

		if ($id) {
			$object->load($id);
		}

		return $this;
	}
}