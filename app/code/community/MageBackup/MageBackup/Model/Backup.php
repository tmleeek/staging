<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup extends Mage_Core_Model_Abstract {
	/** @var MageBackup_MageBackup_Model_Profile $profile */
	protected $profile;
	/** @var MageBackup_MageBackup_Model_Backup_Log $log */
	protected $log;
	/** @var MageBackup_MageBackup_Model_Backup_Zip */
	protected $zip;

	public function getProfile() {
		return $this->profile;
	}

	public function getLog() {
		return $this->log;
	}

	public function getZip() {
		return $this->zip;
	}

	protected function _afterLoad() {
		$this->profile	= Mage::getModel('magebackup/profile')->load($this->getProfileId());

		return parent::_afterLoad();
	}

	/**
	 * Constructor.
	 */
	public function _construct() {
		parent::_construct();

		$this->_init('magebackup/backup');
	}

	/**
	 * Processing object after delete data.
	 *
	 * @return Mage_Core_Model_Abstract
	 */
	protected function _afterDelete() {
		$this->deleteFiles();
		$this->deleteLog();

		return parent::_afterDelete();
	}

	/**
	 * Delete backup files.
	 */
	public function deleteFiles() {
		$filePath	= $this->getFilePath();
		$multipart	= $this->getMultipart();

		for ($i = 0; $i < $multipart; $i++) {
			$file	= Mage::helper('magebackup')->getZipPart($filePath, $i);

			if (is_file($file)) {
				@unlink($file);
			}
		}
	}

	/**
	 * Delete log file.
	 */
	public function deleteLog() {
		$logFile	= $this->profile->getLogDir() . '/magebackup.id.' . $this->getId() . '.log';

		if (is_file($logFile)) {
			unlink($logFile);
		}
	}

	/**
	 * Prepare before backing up.
	 */
	protected function cleanup($final = false) {
		$outputDir	= $this->profile->getOutputDir();

		if (!file_exists($outputDir)) {
			@mkdir($outputDir, 0777, true);
		}

		if (!is_writable($outputDir)) {
			return false;
		}

		if (!file_exists($outputDir . DS . '.htaccess')) {
			file_put_contents($outputDir . DS . '.htaccess', "deny from all");
		}

		// cleanup temp files
		$tmpDir	= $this->profile->getTmpDir();

		if (!file_exists($tmpDir)) {
			@mkdir($tmpDir, 0777, true);
		}

		$handle		= opendir($tmpDir);

		while (($file = @readdir($handle)) !== false) {
			if (is_file($tmpDir . DS . $file)) {
				@unlink($tmpDir . DS . $file);
			}
		}

		@closedir($handle);

		// create log dir
		$logDir	= $this->profile->getLogDir();

		if (!file_exists($logDir)) {
			@mkdir($logDir, 0777, true);
		}

		// cleanup backup files
		$keep = (int) $this->profile->getValue('num_keep');

		if ($keep > 0) {
			$collection	= Mage::getModel('magebackup/backup')->getCollection()
							->addFieldToFilter('profile_id', array('eq' => (int) $this->profile->getId()))
							->setOrder('backup_id', 'DESC')
			;

			foreach ($collection as $backup) {
				$filePath	= $backup->getFilePath();
				$multipart	= $backup->getMultipart();

				if ($keep <= 0) {
					$backup->deleteFiles();
				} else {
					for ($i = 0; $i < $multipart; $i++) {
						$file	= Mage::helper('magebackup')->getZipPart($filePath, $i);

						if (is_file($file)) {
							$keep--;
							break;
						}
					}
				}
			}
		}

		return true;
	}

	protected function finalize() {
		$backupType	= $this->profile->getValue('backup_type');
		$fileName	= $this->getFileName();
		$length		= strlen(Mage::getBaseDir() . '/');
		$outputDir	= $this->profile->getOutputDir();
		$tmpDir		= $this->profile->getTmpDir();

		if ($backupType != MageBackup_MageBackup_Model_Profile_Backuptype::TYPE_DATABASE) {
			// move zip to output dir
			rename($tmpDir . '/' . $fileName, $outputDir . '/' . $fileName);
		}

		if ($backupType != MageBackup_MageBackup_Model_Profile_Backuptype::TYPE_FILE) {
			// Add database file into zip
			$localPath		= $backupType == MageBackup_MageBackup_Model_Profile_Backuptype::TYPE_ALL ? substr($outputDir, $length) . '/' : '';

			foreach (@glob($tmpDir . '/*.sql') as $filePath) {
				$file	= basename($filePath);

				if (is_file($tmpDir . '/' . $file)) {
					$this->zip->addFile($tmpDir . '/' . $file, $localPath . $file);
				}
			}
		}

		$this->zip->finalize();
		$this->cleanup();

		// Total size
		$totalSize	= 0;

		for ($i = 0; $i < $this->zip->getTotalFragments(); $i++) {
			$totalSize +=	@filesize(Mage::helper('magebackup')->getZipPart($outputDir . '/' . $fileName, $i));
		}

		$this->setMultipart($this->zip->getTotalFragments())
			->setTotalSize($totalSize)
			->setEndTime(Mage::getSingleton('core/date')->gmtDate())
			->setStatus('success')
			->save()
		;
	}

	public function cloudUpload() {
		$this->log		= Mage::getSingleton('magebackup/backup_log');
		$engine			= $this->profile->getValue('cloud_engine');

		if ($engine) {
			$cloudEngine	= Mage::getSingleton('magebackup/backup_cloud_' . $engine);

			if ($cloudEngine) {
				$cloudEngine->upload();

				$this->setEndTime(Mage::getSingleton('core/date')->gmtDate())
					->save()
				;
			} else {
				$this->log->error('Cloud engine not found: ' . $engine);
			}
		}
	}

	protected function writeLogHeader() {
		$this->log->info('--------------------------------------------------------------------------------');
		$this->log->info('MageBackup 2.3.0');
		$this->log->info('--------------------------------------------------------------------------------');

		$this->log->info('--- System Information ---');
		$this->log->info('PHP Version         : ' . PHP_VERSION);
		$this->log->info('PHP OS              : ' . PHP_OS);
		$this->log->info('PHP SAPI            : ' . PHP_SAPI);

		if (function_exists('php_uname')) {
			$this->log->info('OS Version          : ' . php_uname('s'));
		}

		if (isset($_SERVER['SERVER_SOFTWARE'])) {
			$server	= $_SERVER['SERVER_SOFTWARE'];
		} else if ($sf = getenv('SERVER_SOFTWARE')) {
			$server	= $sf;
		} else {
			$server	= 'n/a';
		}

		$this->log->info('Web Server:         : ' . $server);
		$this->log->info('Magento Version     : ' . Mage::getVersion());

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->log->info('User Agent          : ' . $_SERVER['HTTP_USER_AGENT']);
		}

		$this->log->info('Safe Mode           : ' . ini_get('safe_mode'));
		$this->log->info('Error Reporting     : ' . Mage::helper('magebackup')->error_reporting());
		$this->log->info('Error Display:      : ' . (ini_get('display_errors') ? 'on' : 'off'));
		$this->log->info('Disabled Functions  : ' . ini_get('disable_functions'));
		$this->log->info('Max Execution Time  : ' . ini_get('max_execution_time'));
		$this->log->info('Memory Limit        : ' . ini_get('memory_limit'));

		if (function_exists('memory_get_usage')) {
			$this->log->info('Memory Usage        : ' . Mage::helper('magebackup')->fileSize(memory_get_usage()));
		}

		if (function_exists('gzcompress')) {
			$this->log->info('GZIP Compression    : Available');
		} else {
			$this->log->info('GZIP Compression    : n/a');
		}

		$this->log->info('Output Directory    : ' . $this->profile->getOutputDir());
		$this->log->info('Backup Filename:    : ' . $this->getFileName());
		$this->log->info('Profile Data        : ' . json_encode($this->profile->getValues()));
	}

	/**
	 * Run backup.
	 */
	public function backup() {
		$this->profile	= Mage::getModel('magebackup/profile')->load($this->getProfileId());
		$this->log		= Mage::getModel('magebackup/backup_log');

		set_error_handler(array($this->log, 'errorHandler'));

		Mage::register('magebackup/backup', $this);

		if (!$this->cleanup()) {
			$this->log->info('Output directory : ' . $this->profile->getOutputDir() . ' is not writable!');
			return false;
		}

		$this->writeLogHeader();
		
		//
		try {
			$fileName		= $this->getFileName();
			$outputDir		= $this->profile->getOutputDir();
			$this->zip		= Mage::getModel('magebackup/backup_zip');

			$this->zip->initialize($outputDir . '/' . $fileName);

			$backupType		= $this->profile->getValue('backup_type');

			if(!($backupType == MageBackup_MageBackup_Model_Profile_Backuptype::TYPE_FILE)) {
				if (!Mage::getSingleton('magebackup/backup_db')->backup()) {
					$this->log->error('Could not backup database');
					return false;
				}
			}

			if (!($backupType == MageBackup_MageBackup_Model_Profile_Backuptype::TYPE_DATABASE)) {
				if (!Mage::getSingleton('magebackup/backup_file')->backup()) {
					$this->log->error('Could not backup files');
					return false;
				}
			}

			$this->finalize();
			$this->cloudUpload();
			$this->cleanup(true);
		} catch (Exception $e) {
			$this->log->error($e->getMessage());
		}

		return true;
	}
}