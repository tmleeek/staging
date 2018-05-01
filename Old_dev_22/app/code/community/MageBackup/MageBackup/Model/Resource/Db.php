<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup db entity resource model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Resource_Db {
	/**
	 * tables Foreign key data array
	 * [tbl_name] = array(create foreign key strings)
	 *
	 * @var array
	 */
	protected $_foreignKeys = array();

	/**
	 * Database connection adapter
	 *
	 * @var Varien_Db_Adapter_Pdo_Mysql
	 */
	protected $_adapter;

	protected $_helper;

	public function getAdapter() {
		return $this->_adapter;
	}

	public function setAdapter($adapter) {
		$this->_adapter	= $adapter;
		$this->_helper	= $this->getResourceHelper()->setAdapter($adapter);

		return $this;
	}

	public function getResourceHelper() {
		$helperClass = Mage::getConfig()->getResourceHelper('magebackup');

		return $helperClass;
	}

	public function __construct($adapter = null) {
		if ($adapter) {
			$this->_adapter	= $adapter;
			$this->_helper	= $this->getResourceHelper()->setAdapter($adapter);
		}
	}

	public function getTableRecords($tableName) {
		return $this->_helper->getTableRecords($tableName);
	}

	/**
	 * Clear data
	 *
	 */
	public function clear() {
		$this->_foreignKeys = array();
	}

	/**
	 * Retrieve table list
	 *
	 * @return array
	 */
	public function getTables() {
		return $this->getAdapter()->listTables();
	}

	/**
	 * Retrieve SQL fragment for drop table
	 *
	 * @param string $tableName
	 * @return string
	 */
	public function getTableDropSql($tableName) {
		return $this->_helper->getTableDropSql($tableName);
	}

	/**
	 * Retrieve SQL fragment for create table
	 *
	 * @param string $tableName
	 * @param bool $withForeignKeys
	 * @return string
	 */
	public function getTableCreateSql($tableName, $withForeignKeys = false) {
		return $this->_helper->getTableCreateSql($tableName, $withForeignKeys = false);
	}

	/**
	 * Retrieve foreign keys for table(s)
	 *
	 * @param string|null $tableName
	 * @return string
	 */
	public function getTableForeignKeysSql($tableName = null) {
		$fkScript = '';
		if (!$tableName) {
			$tables = $this->getTables();
			foreach ($tables as $table) {
				$tableFkScript = $this->_helper->getTableForeignKeysSql($table);
				if (!empty($tableFkScript)) {
					$fkScript .= "\n" . $tableFkScript;
				}
			}
		} else {
			$fkScript = $this->getTableForeignKeysSql($tableName);
		}

		return $fkScript;
	}

	/**
	 * Retrieve table status
	 *
	 * @param string $tableName
	 * @return Varien_Object
	 */
	public function getTableStatus($tableName) {
		$row = $this->getAdapter()->showTableStatus($tableName);

		if ($row) {
			$statusObject = new Varien_Object();
			$statusObject->setIdFieldName('name');
			foreach ($row as $field => $value) {
				$statusObject->setData(strtolower($field), $value);
			}

			$cntRow = $this->getAdapter()->fetchRow(
				$this->getAdapter()->select()->from($tableName, 'COUNT(1) as rows'));
			$statusObject->setRows($cntRow['rows']);

			return $statusObject;
		}

		return false;
	}

	/**
	 * Quote Table Row
	 *
	 * @deprecated
	 *
	 * @param string $tableName
	 * @param array $row
	 * @return string
	 */
	protected function _quoteRow($tableName, array $row) {
		return $row;
	}

	/**
	 * Retrive table partical data SQL insert
	 *
	 * @param string $tableName
	 * @param int $count
	 * @param int $offset
	 * @return string
	 */
	public function getTableDataSql($tableName, $count = null, $offset = null) {
		return $this->_helper->getPartInsertSql($tableName, $count, $offset);
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $tableName
	 * @param unknown_type $addDropIfExists
	 * @return unknown
	 */
	public function getTableCreateScript($tableName, $addDropIfExists = false) {
		return $this->_helper->getTableCreateScript($tableName, $addDropIfExists);;
	}

	/**
	 * Retrieve table header comment
	 *
	 * @param unknown_type $tableName
	 * @return string
	 */
	public function getTableHeader($tableName) {
		$quotedTableName = $this->getAdapter()->quoteIdentifier($tableName);

		return "\n--\n"
		. "-- Table structure for table {$quotedTableName}\n"
		. "--\n\n";
	}

	/**
	 * Return table data dump
	 *
	 * @param string $tableName
	 * @param bool $step
	 * @return string
	 */
	public function getTableDataDump($tableName, $count = null, $offset = null) {
		return $this->getTableDataSql($tableName, $count, $offset);
	}

	/**
	 * Returns SQL header data
	 *
	 * @return string
	 */
	public function getHeader() {
		return $this->_helper->getHeader();
	}

	/**
	 * Returns SQL footer data
	 *
	 * @return string
	 */
	public function getFooter() {
		return $this->_helper->getFooter();
	}

	/**
	 * Retrieve before insert data SQL fragment
	 *
	 * @param string $tableName
	 * @return string
	 */
	public function getTableDataBeforeSql($tableName) {
		return $this->_helper->getTableDataBeforeSql($tableName);
	}

	/**
	 * Retrieve after insert data SQL fragment
	 *
	 * @param string $tableName
	 * @return string
	 */
	public function getTableDataAfterSql($tableName) {
		return $this->_helper->getTableDataAfterSql($tableName);
	}

	/**
	 * Start transaction mode
	 *
	 * @return Mage_Backup_Model_Resource_Db
	 */
	public function beginTransaction() {
		$this->_helper->turnOnSerializableMode();
		$this->getAdapter()->beginTransaction();

		return $this;
	}

	/**
	 * Commit transaction
	 *
	 * @return Mage_Backup_Model_Resource_Db
	 */
	public function commitTransaction() {
		$this->getAdapter()->commit();
		$this->_helper->turnOnReadCommittedMode();

		return $this;
	}

	/**
	 * Rollback transaction
	 *
	 * @return Mage_Backup_Model_Resource_Db
	 */
	public function rollBackTransaction() {
		$this->getAdapter()->rollBack();

		return $this;
	}

	/**
	 * Run sql code
	 *
	 * @param $command
	 * @return Mage_Backup_Model_Resource_Db
	 */
	public function runCommand($command) {
		$this->getAdapter()->query($command);

		return $this;
	}
}