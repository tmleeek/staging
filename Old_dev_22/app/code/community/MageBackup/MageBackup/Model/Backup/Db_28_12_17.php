<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup db model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Db {
	protected $_connections;
	protected $_resources;
	protected $_maindb;

	/** @var	MageBackup_MageBackup_Model_Backup	$backup */
	protected $backup;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->backup	= Mage::registry('magebackup/backup');

		$this->backup->getLog()->debug(get_called_class() . ' :: New instance');
	}

	public function getMainDb() {
		if (!$this->_maindb) {
			$config			= Mage::getConfig()->getResourceConnectionConfig('default_setup');
			$this->_maindb	= (string) $config->dbname;
		}

		return $this->_maindb;
	}

	public function getConnection($dbname = '') {
		if (!$dbname) {
			$dbname	= $this->getMainDb();
		}

		if (!isset($this->_connections[$dbname])) {
			$config			= Mage::getConfig()->getResourceConnectionConfig('default_setup');
			$config->dbname	= $dbname;

			$this->_connections[$dbname]	= $this->_newConnection($config);
		}

		return $this->_connections[$dbname];
	}

	protected function _newConnection($config) {
		if ($config instanceof Mage_Core_Model_Config_Element) {
			$config = $config->asArray();
		}
		if (!is_array($config)) {
			return false;
		}

		$connection = false;
		$type		= $config['type'];

		// try to get adapter and create connection
		$className = $this->_getConnectionAdapterClassName($type);
		if ($className) {
			// define profiler settings
			$config['profiler'] = isset($config['profiler']) && $config['profiler'] != 'false';

			$connection = new $className($config);
			if ($connection instanceof Varien_Db_Adapter_Interface) {
				// run after initialization statements
				if (!empty($config['initStatements'])) {
					$connection->query($config['initStatements']);
				}
			} else {
				$connection = false;
			}
		}

		return $connection;
	}

	protected function _getConnectionAdapterClassName($type) {
		$config = Mage::getConfig()->getResourceTypeConfig($type);
		if (!empty($config->adapter)) {
			return (string)$config->adapter;
		}

		return false;
	}

	public function getResource($dbname = '') {
		if (!$dbname) {
			$dbname	= $this->getMainDb();
		}

		if (!isset($this->_resources[$dbname])) {
			$connection					= $this->getConnection($dbname);
			$this->_resources[$dbname]	= Mage::getResourceModel('magebackup/db', $connection);
		}

		return $this->_resources[$dbname];
	}

	public function backup() {
		if (!$this->backup) {
			return false;
		}

		$excludedTables		= $this->backup->getProfile()->getExcludedTables();
		$skippedTables		= $this->backup->getProfile()->getSkippedTables();
		$includedDatabases	= explode(',', $this->backup->getProfile()->getValue('included_databases'));
		$maindb				= $this->getMainDb();

		try {
			$this->dump($maindb, $excludedTables, $skippedTables);

			foreach ($includedDatabases as $db) {
				if (!empty($db)) {
					$this->dump($db);
				}
			}
		} catch (Exception $e) {
			$this->backup->getLog()->error($e->getMessage());

			return false;
		}

		return true;
	}

	protected function dump($database, $excluded = false, $skipped = false) {
		$log		= $this->backup->getLog();
		$numQueries	= $this->backup->getProfile()->getValue('ajax_num_queries', 5000);
		$tmpDir		= $this->backup->getProfile()->getTmpDir() . '/';
		$dumpFile	= $tmpDir . ($database == $this->getMainDb() ? 'database' : $database) . '.sql';
		$resource	= $this->getResource($database);
		$tables		= $resource->getTables();
		$start		= 0;
		$numRecords	= 0;

		$log->debug('Dumping database ' . $database . ' - ' . count($tables) . ' table(s).');

		$fp			= fopen($dumpFile, 'w+');
		fwrite($fp, $resource->getHeader());

		if (count($tables)) {
			$table		= reset($tables);

			while ($table) {
				$fp			= is_resource($fp) ? $fp : fopen($dumpFile, 'a+');
				$fullTable	= $database . '.' . $table;
				$i			= 0;

				while ($i < $numQueries) {
					if ($start == 0) {
						if (!$excluded || !in_array($table, $excluded)) {
							$log->debug('Start dumping table: ' . $database . '.' . $table);

							$i++;
							fwrite($fp, $resource->getTableHeader($table));
							fwrite($fp, $resource->getTableCreateScript($table, true));

							if (!$skipped || !in_array($table, $skipped)) {
								$numRecords	= $resource->getTableRecords($table);
							} else {
								$numRecords	= false;
							}
						} else {
							$table	= next($tables);
							break;
						}
					}

					if ($numRecords) {
						if ($start == 0) {
							$log->debug('Start dumping data of table:' . $database . '.' . $table);

							fwrite($fp, $resource->getTableDataBeforeSql($table));
						}

						if ($numRecords - $start + $i > $numQueries) {
							$count	= $numQueries - $i;
						} else {
							$count	= $numRecords;
						}

						$current	= $count + $start;
						$i			+= $current;

						$log->debug('Dumping data of table: ' . $database . '.' . $table . ' from ' . ($start + 1) . ' to ' . $current);

						fwrite($fp, $resource->getTableDataDump($table, $count, $start));

						if ($count + $start >= $numRecords) {
							$log->debug('Finish dumping data of table: ' . $database . '.' . $table);

							$current	= 0;
							fwrite($fp, $resource->getTableDataAfterSql($table));
						}

						$start		= $current;
					}

					if ($start == 0) {
						$table	= next($tables);

						if (!$table) {
							fwrite($fp, $resource->getFooter());
							fclose($fp);
							break;
						}
					}
				}

				if (is_resource($fp)) {
					fclose($fp);
				}
			}
		}
	}
}