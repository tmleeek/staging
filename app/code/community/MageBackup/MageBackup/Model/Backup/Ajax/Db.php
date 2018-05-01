<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup ajax db model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Ajax_Db extends MageBackup_MageBackup_Model_Backup_Db {
	/** @var	MageBackup_MageBackup_Model_Backup_Ajax	$backup */
	protected $backup;

	protected $databases;
	protected $database;
	protected $table;
	protected $record;
	protected $countDb;
	protected $countRecords;
	protected $processedRecords;

	public function __construct() {
		parent::__construct();

		$session				= $this->backup->getSession();

		$this->databases		= $session->getData('db.databases');
		$this->database			= $session->getData('db.database');
		$this->table			= $session->getData('db.table');
		$this->record			= $session->getData('db.record');
		$this->countRecords		= $session->getData('db.countRecords');
		$this->processedRecords	= $session->getData('db.processedRecords');
	}

	protected function clearStatistic() {
		$session	= $this->backup->getSession();

		$session->unsetData('db.databases');
		$session->unsetData('db.database');
		$session->unsetData('db.table');
		$session->unsetData('db.record');
		$session->unsetData('db.countRecord');
		$session->unsetData('db.processedRecords');
	}

	protected function saveStatistic($database = '', $table = '', $start = '') {
		$session	= $this->backup->getSession();

		$session->setData('db.database', $database);
		$session->setData('db.table', $table);
		$session->setData('db.record', $start);
	}

	protected function getDumpingPercent() {
		$percent	= 80 * $this->processedRecords / $this->countRecords;
		$percent	= $percent < 80 ? $percent : 80;

		return $percent;
	}

	protected function scan() {
		$session			= $this->backup->getSession();
		$excludedTables		= $this->backup->getProfile()->getExcludedTables();
		$skippedTables		= $this->backup->getProfile()->getSkippedTables();
		$includedDatabases	= $this->backup->getProfile()->getIncludedDatabases();
		$mainDb				= $this->getMainDb();

		$this->scanDb($mainDb, $excludedTables, $skippedTables);

		foreach ($includedDatabases as $db) {
			if (!empty($db)) {
				$this->scanDb($db);
			}
		}

		$response			= $this->backup->getResponse();
		$response->info		= Mage::helper('magebackup')->__('Scanning database finished: %d database(s) - %d records(s)', $this->countDb, $this->countRecords);
		$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_DATABASES, 20);

		$session->setData('db.countRecords', $this->countRecords);
		$session->setData('db.databases', $this->databases);
	}

	protected function scanDb($database, $excluded = null, $skipped = null) {
		$this->countDb++;

		$resource					= $this->getResource($database);
		$this->databases[$database]	= array();
		$tables						= $resource->getTables();
		$totalRecords				= 0;
		$log						= $this->backup->getLog();

		foreach ($tables as $table) {
			$fullTable	= $database . '.' . $table;

			if (!$excluded || !in_array($table, $excluded)) {
				if (!$skipped || !in_array($table, $skipped)) {
					$countRecords	= $resource->getTableRecords($table);
				} else {
					$countRecords	= 0;

					$log->info('Content of table ' . $fullTable . ' is skipped from dumping');
				}

				$totalRecords						+= $countRecords;
				$this->countRecords					+= $countRecords;
				$this->databases[$database][$table]	= $countRecords;
			} else {
				$log->info('Table ' . $fullTable . ' is ignored from dumping');
			}
		}

		$log->info('Finish scanning database: ' . $database . ' - ' . count($tables) . ' table(s) - ' . $totalRecords . ' record(s)');
	}

	protected function dumpAjax() {
		$log			= $this->backup->getLog();
		$databases		= $this->databases;
		$numQueries		= $this->backup->getProfile()->getValue('ajax_num_queries', 5000);
		$tmpDir			= $this->backup->getProfile()->getTmpDir() . '/';
		$i				= 0;
		$database		= $this->database;
		$table			= $this->table;
		$start			= $this->record;
		$current		= 0;

		/** @var MageBackup_MageBackup_Helper_Data $helper */
		$helper			= Mage::helper('magebackup');

		if (!$database) {
			$database	= $helper->getFirstkey($databases);
		}

		$dumpFile		= $tmpDir . ($database == $helper->getFirstKey($databases) ? 'database' : $database) . '.sql';
		$resource		= $this->getResource($database);

		if (!$table) {
			$table		= $helper->getFirstKey($databases[$database]);
		}

		if ($start == 0) {
			if ($table == $helper->getFirstKey($databases[$database])) {
				$fp		= fopen($dumpFile, 'w+');

				if (count($databases[$database])) {
					$log->debug('Start dumping database: ' . $database);

					fwrite($fp, $resource->getHeader());
				}
			}
		}

		$fp	= isset($fp) ? $fp : fopen($dumpFile, 'a+');

		//
		if (!$table) {
			// done db
			$log->debug('Finish dumping database: ' . $database);
			fwrite($fp, $resource->getFooter());
			fclose($fp);

			$percent	= $this->getDumpingPercent();
			$response	= $this->backup->getResponse();

			$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_DATABASES, 20 + $percent);
			$response->info		= $helper->__('Dumping finished database: %s', $database);

			if ($database = $helper->getNextKey($databases, $database)) {
				$this->saveStatistic($database);

				return false;
			} else {
				$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_DATABASES, 100);
				$this->clearStatistic();

				return true;
			}
		}

		while ($i < $numQueries) {
			if (isset($databases[$database][$table])) {
				$numRecords	= $databases[$database][$table];

				if ($start == 0) {
					$log->debug('Start dumping table: ' . $database . '.' . $table);

					$i++;
					fwrite($fp, $resource->getTableHeader($table));
					fwrite($fp, $resource->getTableCreateScript($table, true));
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

				if ($start == 0 && !($table = $helper->getNextKey($databases[$database], $table))) {
					$log->debug('Finish dumping database: ' . $database);

					fwrite($fp, $resource->getFooter());
					fclose($fp);

					$this->processedRecords	+= $i;

					$percent	= $this->getDumpingPercent();
					$response	= $this->backup->getResponse();

					$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_DATABASES, 20 + $percent);
					$response->info		= $helper->__('Dumping finished database: %s', $database);

					if ($database = $helper->getNextKey($databases, $database)) {
						$this->saveStatistic($database);

						return false;
					} else {
						$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_DATABASES, 100);
						$this->clearStatistic();

						return true;
					}
				}
			}
		}

		$this->saveStatistic($database, $table, $start);

		$this->processedRecords	+= $i;

		$percent	= $this->getDumpingPercent();
		$response	= $this->backup->getResponse();

		$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_DATABASES, 20 + $percent);
		$response->info		= $helper->__('Dumping %s.%s at record #%d', $database, $table, $current);

		fclose($fp);

		return false;
	}

	/////////////////////////////////////////////////////////////////
	public function initialize() {
		$this->clearStatistic();
	}

	public function backup() {
		$response		= $this->backup->getResponse();
		$response->info	= '';
		$response->step	= MageBackup_MageBackup_Model_Backup_Ajax::STEP_DATABASES;

		if (!$this->databases) {
			$this->scan();
			$response->nextstep	= $response->step;
		} else {
			if ($this->dumpAjax()) {
				$response->nextstep	= $this->backup->getNextStep($response->step);
			} else {
				$response->nextstep	= $response->step;
			}
		}
	}
}