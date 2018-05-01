<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup ajax file model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Ajax_File extends MageBackup_MageBackup_Model_Backup_File {
	/** @var	MageBackup_MageBackup_Model_Backup_Ajax	$backup */
	protected $backup;

	protected $dirs;
	protected $count;
	protected $size;
	protected $files;
	protected $currentIndex;
	protected $scanTimeStart;

	public function __construct() {
		parent::__construct();
		
		$session			= $this->backup->getSession();
		
		$this->files		= $session->getData('file.files');
		$this->files		= is_array($this->files) ? $this->files : array();
		$this->dirs			= $session->getData('file.dirs');
		$this->count		= $session->getData('file.count');
		$this->size			= $session->getData('file.size');
		$this->currentIndex	= $session->getData('file.currentIndex');
	}

	protected function clearStatistic() {
		$session	= $this->backup->getSession();

		$session->unsetData('file.dirs');
		$session->unsetData('file.files');
		$session->unsetData('file.count');
		$session->unsetData('file.size');
	}

	protected function scan() {
		$session	= $this->backup->getSession();

		if (!$this->scanTimeStart) {
			$this->scanTimeStart	= time();
		}

		if (!$this->dirs && !count($this->files)) {
			$this->clearStatistic();
			$this->dirs[]	= Mage::helper('magebackup')->cleanPath(Mage::getBaseDir());
		}

		foreach ($this->dirs as $key => $startDir) {
			if ($startDir != '') {
				unset($this->dirs[$key]);
				
				if (!$this->isExcluded($startDir)) {
					$this->addFileScan($startDir);
					$this->scanDir($startDir);
				}
			}
		}

		if ((time() - $this->scanTimeStart) < 5 && count($this->dirs)) {
			$this->scan();
		} else {
			unset($this->scanTimeStart);
		}

		$reponse	= $this->backup->getResponse();
		$helper		= Mage::helper('magebackup');

		if (count($this->dirs)) {
			$reponse->info	= $helper->__('Scanning folder: %s<br />Status: %d file(s) - %s', $helper->cleanPath($startDir), $this->count, $helper->fileSize($this->size));
		} else {
			$reponse->info	= $helper->__('Scanning files finished.<br />Status: %d file(s) - %s', $this->count, $helper->fileSize($this->size));
		}

		$reponse->progress	= $this->backup->calculatePercent('files', 20);

		$session->setData('file.count', $this->count);
		$session->setData('file.size', $this->size);
		$session->setData('file.files', $this->files);
		$session->setData('file.dirs', $this->dirs);

		return !count($this->dirs);
	}

	protected function scanDir($dir) {
		$log	= $this->backup->getLog();

		if (is_dir($dir)) {
			$log->debug('Scanning directory: ' . $dir);

			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..') {
						$filePath	= $dir . '/' . $file;

						if (!$this->isExcluded($filePath)) {
							if (is_dir($filePath) && !$this->isSkippedDir($dir)) {
								$this->dirs[] = $filePath;
							} else if (is_file($filePath) && !$this->isSkippedFile($dir)) {
								$this->addFileScan($filePath);
							} else {
								$log->info((is_dir($filePath) ? 'Directory' : 'File') . ': ' . $filePath . ' is skipped from packing');
							}
						} else {
							$log->info((is_dir($filePath) ? 'Directory' : 'File') . ': ' . $filePath . ' is ignored from packing');
						}
					}
				}
			} else {
				$log->error('Could not open directory: ' . $dir);
			}
		} else {
			$log->error($dir . ' is not directory');
		}
	}

	protected function addFileScan($file) {
		$file	= Mage::helper('magebackup')->cleanPath($file);

		$size	= filesize($file);

		if (is_file($file) && ($limit = $this->backup->getProfile()->getExcludedFileSize()) > 0) {
			if ($size > $limit) {
				return;
			}
		}

		$this->size		+= $size;
		$this->count++;
		$this->files[]	= $file;
	}

	protected function archive() {
		$session	= $this->backup->getSession();
		$fileName	= $this->backup->getFileName();
		$numFiles	= $this->backup->getProfile()->getValue('ajax_num_files', 2500);
		$length		= strlen(Mage::getBaseDir() . '/');
		$outputDir	= $this->backup->getProfile()->getOutputDir();
		$zip		= $this->backup->getZip();
		$maxTime	= $this->backup->getProfile()->getValue('ajax_max_time', 60);
		$maxTime	= $maxTime < 10 ? 10 : $maxTime;
		$timeStart	= time();

		for ($i = (int) $this->currentIndex; $i < $numFiles + $this->currentIndex && $i < $this->count; $i++) {
			if (time() - $timeStart >= $maxTime - 5) {
				break;
			}

			$file	= $this->files[$i];

			$zip->addFile($file, substr($file, $length));
		}
		
		$session->setData('file.currentIndex', $i);

		$totalSize	= 0;

		for ($index = 0; $index <= $this->backup->getZip()->getTotalFragments(); $index++) {
			$part		= Mage::helper('magebackup')->getZipPart($outputDir . '/' . $fileName, $index);

			if (is_file($part)) {
				$totalSize += @filesize($part);
			}
		}
		
		$response	= $this->backup->getResponse();
		
		if ($i >= $this->count) {
			$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_FILES, 100);
			$response->info		= Mage::helper('magebackup')->__('Archiving files finished.<br />Current Archive File: %s - Total Size: %s', $fileName, Mage::helper('magebackup')->fileSize($totalSize));

			$this->clearStatistic();
			return true;
		} else {
			$percent	= 80 * $i / $this->count;
			$percent	= $percent < 80 ? $percent : 80;

			$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_FILES, 20 + $percent);
			$response->info		= Mage::helper('magebackup')->__('Archiving %d/%d file(s).<br />Recently added file: %s<br />Current Archive File: %s - Total Size: %s', $i, $this->count, $file, basename($zip->getDataFileName()), Mage::helper('magebackup')->fileSize($totalSize));

			return false;
		}
		
	}

	/////////////////////////////////////////////////////////////////
	public function initialize() {
		$this->clearStatistic();
	}

	public function backup() {
		$response	= $this->backup->getResponse();
		$response->info	= '';
		$response->step	= MageBackup_MageBackup_Model_Backup_Ajax::STEP_FILES;

		if (!$this->count || count($this->dirs)) {
			$this->scan();

			$response->nextstep	= $response->step;
		} else {
			if ($this->archive()) {
				$response->nextstep	= $this->backup->getNextStep($response->step);
			} else {
				$response->nextstep	= $response->step;
			}
		}
	}
}