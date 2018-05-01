<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup cloud model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Cloud {
	/** @var	MageBackup_MageBackup_Model_Backup|MageBackup_MageBackup_Model_Backup_Ajax	$backup */
	protected $backup;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->backup	= Mage::registry('magebackup/backup');

		$this->backup->getLog()->debug(get_called_class() . ' :: New instance');
	}

	/**
	 * Upload backup part file to cloud.
	 *
	 * @param	string	$file	Full path of file to upload.
	 * @return	bool
	 */
	protected function uploadPart($file) {
		return true;
	}

	/**
	 * Delete backup part file from local.
	 *
	 * @param	string	$file	Full path of file to delete.
	 */
	protected function deletePart($file) {
		$profile	= $this->backup->getProfile();

		if ($profile->getValue('cloud_delete_local')) {
			if (is_file($file)) {
				$this->backup->getLog()->debug(get_called_class() . ' :: Deleting file: ' . $file);

				@unlink($file);
			}
		}
	}

	/**
	 * Upload backup to cloud.
	 *
	 * @return string
	 */
	public function upload() {
		$totalParts	= $this->backup->getMultipart();
		$filePath	= $this->backup->getFilePath();
		$log		= $this->backup->getLog();

		try {
			for ($i = 0; $i < $totalParts; $i++) {
				$file	= Mage::helper('magebackup')->getZipPart($filePath, $i);

				if (file_exists($file)) {
					$log->debug(get_called_class() . ' :: Uploading file: ' . $file);

					if ($this->uploadPart($file)) {
						$log->debug(get_called_class() . ' :: Upload successfully: ' . $file);

						$this->deletePart($file);
					} else {
						$log->error(get_called_class() . ' :: Upload failed: ' . $file);
					}
				} else {
					$log->warning(get_called_class() . ' :: File does not exist: ' . $file);
				}
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			$this->backup->getLog()->error($e->getMessage());

			return $e->getMessage();
		}

		return '';
	}

	/**
	 * Ajax upload backup to cloud.
	 */
	public function ajaxUpload() {
		$session	= $this->backup->getSession();
		$part		= $session->getData('cloud.part', 0);
		$totalPart	= $this->backup->getMultipart();
		$filePath	= $this->backup->getFilePath();
		$response	= $this->backup->getResponse();
		$log		= $this->backup->getLog();

		$file		= Mage::helper('magebackup')->getZipPart($filePath, $part);

		try {
			if (is_file($file)) {
				$log->debug(get_called_class() . ' :: Uploading file: ' . $file);

				if ($this->uploadPart($file)) {
					$log->debug(get_called_class() . ' :: Upload successfully: ' . $file);

					$this->deletePart($file);
				} else {
					$log->error(get_called_class() . ' :: Upload failed: ' . $file);
				}
			} else {
				$log->warning(get_called_class() . ' :: File does not exist: ' . $file);
			}
		} catch (Exception $e) {
			$this->backup->getLog()->error($e->getMessage());

			$response->error	= $e->getMessage();
		}

		$response->step		= MageBackup_MageBackup_Model_Backup_Ajax::STEP_CLOUD;
		$part++;

		if ($part < $totalPart) {
			$response->nextstep	= $response->step;
			$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_CLOUD, $part / $totalPart);
			$response->info		= Mage::helper('magebackup')->__('Upload file %s successfully', basename($file));

			$session->setData('cloud.part', $part);
		} else {
			$response->nextstep	= MageBackup_MageBackup_Model_Backup_Ajax::STEP_DONE;
			$response->progress	= $this->backup->calculatePercent(MageBackup_MageBackup_Model_Backup_Ajax::STEP_CLOUD, 100);
			$response->info		= Mage::helper('magebackup')->__('Upload backup successfully. Total: %d part(s)', $totalPart);
		}
	}
}