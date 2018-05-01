<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup file model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_File {
	/** @var	MageBackup_MageBackup_Model_Backup	$backup */
	protected $backup;

	protected $lastPingTime;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->backup	= Mage::registry('magebackup/backup');

		$this->backup->getLog()->debug(get_called_class() . ' :: New instance');
	}

	public function backup() {
		if (!$this->backup) {
			return false;
		}

		try {
			$this->addDir(Mage::helper('magebackup')->cleanPath(Mage::getBaseDir()));
		} catch (Exception $e) {
			$this->backup->getLog()->error($e->getMessage());

			return false;
		}

		return true;
	}

	protected function isExcluded($file) {
		$filePath	= substr($file, strlen(Mage::getBaseDir() . '/'));

		return $filePath && in_array($filePath, $this->backup->getProfile()->getExcludedFiles());
	}

	protected function isSkippedDir($dir) {
		$path	= substr($dir, strlen(Mage::getBaseDir() . '/'));

		return $path && in_array($path, $this->backup->getProfile()->getSkippedDirs());
	}

	protected function isSkippedFile($dir) {
		$path	= substr($dir, strlen(Mage::getBaseDir() . '/'));

		return $path && in_array($path, $this->backup->getProfile()->getSkippedFiles());
	}

	protected function addFile($root, $file) {
		// ping mysql server to avoid server has gone away error
		if (!$this->lastPingTime || time() - $this->lastPingTime > 5) {
			Mage::getSingleton('core/resource')->getConnection('core_write')->query('SELECT 1');

			$this->lastPingTime	= time();
		}

		$filePath	= realpath($root . '/' . $file);

		$this->backup->getZip()->addFile($filePath, $file);
	}

	protected function addDir($root, $path = '') {
		$dir		= $root . '/' . ($path ? $path : '');
		$handle		= opendir($dir);
		$log		= $this->backup->getLog();

		while (($file = readdir($handle)) !== false) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			$filePath	= ($path ? $path . '/' : '') . $file;
			$filePath	= str_replace(DS, '/', $filePath);
			$fullPath	= $root . '/' . $filePath;
			$isDir		= is_dir($fullPath);

			if (!$this->isExcluded($fullPath)) {
				if ($isDir && !$this->isSkippedDir($dir)) {
					$this->addFile($root, $filePath);
					$this->addDir($root, $filePath);
				} else if (!$isDir && !$this->isSkippedFile($dir)) {
					$this->addFile($root, $filePath);
				} else {
					$log->info(($isDir ? 'Directory' : 'File') . ': ' . $fullPath . ' is skipped from packing');
				}
			} else {
				$log->info(($isDir ? 'Directory' : 'File') . ': ' . $fullPath . ' is ignored from packing');
			}
		}

		closedir($handle);
	}
}