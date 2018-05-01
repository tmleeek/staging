<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup ajax model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Ajax extends MageBackup_MageBackup_Model_Backup {
	const STEP_INITIALIZE		= 'initialize';
	const STEP_DATABASES		= 'databases';
	const STEP_FILES			= 'files';
	const STEP_FINALIZE			= 'finalize';
	const STEP_CLOUD			= 'cloud';
	const STEP_DONE				= 'done';

	/** @var  MageBackup_MageBackup_Model_Backup_Ajax_Response	$response */
	protected $response;
	/** @var  MageBackup_MageBackup_Model_Session	$session */
	protected $session;
	protected $steps;

	/**
	 * Constructor.
	 */
	public function _construct() {
		parent::_construct();

		$this->session	= Mage::getSingleton('magebackup/session');
		$this->response	= Mage::getSingleton('magebackup/backup_ajax_response');
	}

	public function getResponse() {
		return $this->response;
	}

	public function getSession() {
		return $this->session;
	}
	
	public function getSteps() {
		if (!$this->steps) {
			$helper								= Mage::helper('magebackup');
			$this->steps						= array();
			$this->steps[self::STEP_INITIALIZE]	= $helper->__('Initializing backup process');
			$backupType							= $this->profile->getValue('backup_type');

			if ($backupType == MageBackup_MageBackup_Model_Profile_Backuptype::TYPE_ALL) {
				$this->steps[self::STEP_DATABASES]	= $helper->__('Backing up databases');
				$this->steps[self::STEP_FILES]		= $helper->__('Backing up files');
			} else if ($backupType == MageBackup_MageBackup_Model_Profile_Backuptype::TYPE_DATABASE) {
				$this->steps[self::STEP_DATABASES]	= $helper->__('Backing up databases');
			} else {
				$this->steps[self::STEP_FILES]		= $helper->__('Backing up files');
			}

			$this->steps[self::STEP_FINALIZE]	= $helper->__('Finalizing the backup process');

			if ($this->profile->getValue('cloud_engine')) {
				$this->steps[self::STEP_CLOUD]	= $helper->__('Uploading to cloud');
			}
		}

		return $this->steps;
	}

	public function getNextStep($step) {
		$steps	= $this->getSteps();

		return Mage::helper('magebackup')->getNextKey($steps, $step);
	}

	public function calculatePercent($step, $percent) {
		$count				= count($this->getSteps());
		$calculatedPercent	= 0;

		foreach ($this->getSteps() as $s => $label) {
			if ($s != $step) {
				$calculatedPercent	+= 100 / $count;
			} else {
				$calculatedPercent	+= $percent / $count;
				break;
			}
		}

		return round($calculatedPercent, 5);
	}
	
	public function backup() {
		$response	= $this->response;

		if ($this->log) {
			set_error_handler(array($this->log, 'errorHandler'));
		}

		switch ($response->nextstep) {
			default:
			case self::STEP_INITIALIZE:
				$this->initialize();
				break;
			case self::STEP_DATABASES:
				Mage::getSingleton('magebackup/backup_ajax_db')->backup();
				break;
			case self::STEP_FILES:
				Mage::getSingleton('magebackup/backup_ajax_file')->backup();
				break;
			case self::STEP_FINALIZE:
				$this->finalize();
				break;
			case self::STEP_CLOUD:
				$this->cloudUpload();
				break;
			case self::STEP_DONE:
				$this->done();
				break;
		}
	}

	public function initialize() {
		$this->log		= Mage::getModel('magebackup/backup_log');
		$response		= $this->response;

		if (!$this->cleanup()) {
			$this->log->info('Output directory : ' . $this->profile->getOutputDir() . ' is not writable!');

			$response->error	= Mage::helper('magebackup')->__('Output directory : %s is not writable!', $this->profile->getOutputDir());

			return false;
		}

		$this->writeLogHeader();

		//
		$fileName		= $this->getFileName();
		$outputDir		= $this->profile->getOutputDir();
		$this->zip		= Mage::getModel('magebackup/backup_zip');

		$this->zip->initialize($outputDir . '/' . $fileName);

		$backupType		= $this->profile->getValue('backup_type');

		if ($backupType != MageBackup_MageBackup_Model_Profile_Backuptype::TYPE_DATABASE) {
			Mage::getSingleton('magebackup/backup_ajax_file')->initialize();
		}

		if ($backupType != MageBackup_MageBackup_Model_Profile_Backuptype::TYPE_FILE) {
			Mage::getSingleton('magebackup/backup_ajax_db')->initialize();
		}

		$response->step		= self::STEP_INITIALIZE;
		$response->nextstep	= $this->getNextStep($response->step);
		$response->info		= Mage::helper('magebackup')->__('Initializing backup finished');
		$response->progress	= $this->calculatePercent($response->step, 100);
	}

	public function finalize() {
		parent::finalize();

		$response		= $this->getResponse();

		if ($this->profile->getValue('cloud_engine')) {
			$response->nextstep	= self::STEP_CLOUD;
		} else {
			$response->nextstep	= self::STEP_DONE;
		}

		$response->step		= self::STEP_FINALIZE;
		$response->progress	= $this->calculatePercent($response->step, 100);
		$response->info		= Mage::helper('magebackup')->__('Finalizing backup finished.');

		if ($response->nextstep == self::STEP_CLOUD) {
			$response->info	.= '<br />' . Mage::helper('magebackup')->__('Uploading to cloud storage.');
		}
	}

	public function cloudUpload() {
		$response		= $this->getResponse();
		$response->info	= '';
		$engine			= $this->profile->getValue('cloud_engine');

		if ($engine) {
			$cloudEngine	= Mage::getSingleton('magebackup/backup_cloud_' . $engine);

			if ($cloudEngine) {
				$cloudEngine->ajaxUpload();
			} else {
				$this->log->error('Cloud engine not found: ' . $engine);
				$response->error	= Mage::helper('magebackup')->__('Could not find cloud engine: %s', $engine);
			}
		}
	}

	public function done() {
		$this->cleanup(true);
		$this->setEndTime(Mage::getSingleton('core/date')->gmtDate())
			->save()
		;

		Mage::getSingleton('magebackup/session')->clear();

		$response			= $this->getResponse();
		$response->error	= '';
		$response->done		= true;
		$response->progress	= 100;
		$response->info		= '';
	}
}