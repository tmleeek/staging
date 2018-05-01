<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Profile extends Mage_Core_Model_Abstract {
	protected $outputDir;
	protected $tmpDir;
	protected $logDir;
	protected $archiveName;
	protected $excludedFiles		= array();
	protected $skippedFiles			= array();
	protected $skippedDirs			= array();
	protected $excludedTables		= array();
	protected $skippedTables		= array();
	protected $includedDatabases	= array();

	/**
	 * Constructor.
	 */
	public function _construct() {
		parent::_construct();

		$this->_init('magebackup/profile');
	}

	/**
	 * Processing object after load data.
	 *
	 * @return	Mage_Core_Model_Abstract
	 */
	protected function _afterLoad() {
		$profileId	= $this->getId();
		$collection	= Mage::getModel('magebackup/data')->getCollection()->setProfileId($profileId);
		$data		= array();

		foreach ($collection as $item) {
			$data[$item->getName()]	= $item->getValue();
		}

		$this->setData('data', $data);

		return parent::_afterLoad();
	}

	protected function _getSchedule() {
		$frequency	= $this->getValue('cron_frequency');
		$hours		= $this->getValue('cron_hour');

		$schedule = "0 $hours ";

		switch ($frequency) {
			case MageBackup_MageBackup_Model_Profile_Cron::CRON_DAILY:
				$schedule .= "* * *";
				break;
			case MageBackup_MageBackup_Model_Profile_Cron::CRON_WEEKLY:
				$schedule .= "* * 1";
				break;
			case MageBackup_MageBackup_Model_Profile_Cron::CRON_MONTHLY:
				$schedule .= "1 * *";
				break;
			default:
				$schedule .= "* */1 *";
				break;
		}

		return $schedule;
	}

	/**
	 * Processing object after save data.
	 *
	 * @return	Mage_Core_Model_Abstract
	 */
	protected function _afterSave() {
		$profileId	= $this->getId();
		$exprPath	= 'crontab/jobs/magebackup_magebackup_profile_' . $profileId . '/schedule/cron_expr';
		$paramPath	= 'crontab/jobs/magebackup_magebackup_profile_' . $profileId . '/parameters';
		$modelPath	= 'crontab/jobs/magebackup_magebackup_profile_' . $profileId . '/run/model';
		$activePath	= 'crontab/jobs/magebackup_magebackup_profile_' . $profileId . '/is_active';

		if ($this->getValue('cron_enable')) {
			$expr	= $this->_getSchedule();

			Mage::getModel('core/config_data')->load($exprPath, 'path')
				->setValue($expr)
				->setPath($exprPath)
				->save()
			;

			$params	= array(
				'id'	=> $profileId
			);

			Mage::getModel('core/config_data')->load($paramPath, 'path')
				->setValue(Mage::helper('core')->jsonEncode($params))
				->setPath($paramPath)
				->save()
			;

			Mage::getModel('core/config_data')->load($modelPath, 'path')
				->setValue('magebackup/observer::backup')
				->setPath($modelPath)
				->save()
			;

			Mage::getModel('core/config_data')->load($activePath, 'path')
				->setValue(1)
				->setPath($activePath)
				->save()
			;
		} else {
			Mage::getModel('core/config_data')->load($exprPath, 'path')->delete();
			Mage::getModel('core/config_data')->load($paramPath, 'path')->delete();
			Mage::getModel('core/config_data')->load($modelPath, 'path')->delete();
			Mage::getModel('core/config_data')->load($activePath, 'path')->delete();
		}

		
		return parent::_afterSave();
	}

	/**
	 * Processing object after delete data.
	 *
	 * @return Mage_Core_Model_Abstract
	 */
	protected function _afterDelete() {
		$collection	= Mage::getModel('magebackup/data')->getCollection()->setProfileId($this->getId());

		foreach ($collection as $data) {
			$data->delete();
		}

		$profileId	= $this->getId();
		$exprPath	= 'crontab/jobs/magebackup_magebackup_profile_' . $profileId . '/schedule/cron_expr';
		$paramPath	= 'crontab/jobs/magebackup_magebackup_profile_' . $profileId . '/parameters';
		$modelPath	= 'crontab/jobs/magebackup_magebackup_profile_' . $profileId . '/run/model/';
		$activePath	= 'crontab/jobs/magebackup_magebackup_profile_' . $profileId . '/is_active';

		Mage::getModel('core/config_data')->load($exprPath, 'path')->delete();
		Mage::getModel('core/config_data')->load($paramPath, 'path')->delete();
		Mage::getModel('core/config_data')->load($modelPath, 'path')->delete();
		Mage::getModel('core/config_data')->load($activePath, 'path')->delete();

		return parent::_afterDelete();
	}

	/**
	 * Get all profile data.
	 *
	 * @return	array
	 */
	public function getValues() {
		return $this->getData('data');
	}

	/**
	 * Get profile data value
	 *
	 * @param	string	$key	The data key
	 * @param	mixed	$default
	 * 
	 * @return	string
	 */
	public function getValue($key, $default = null) {
		$data	= $this->getData('data');
		
		return isset($data[$key]) ? $data[$key] : $default;
	}

	/**
	 * Set default values for profile.
	 */
	public function setDefaultValues() {
		$data	= array(
			'output_directory'		=> '{{base_dir}}/var/magebackup',
			'backup_files'			=> 1,
			'backup_database'		=> 1,
			'ajax_time_limit'		=> 60,
			'ajax_num_files'		=> 2500,
			'ajax_num_queries'		=> 10000,
			'cron_keep_backups'		=> 0,
			'cloud_delete_local'	=> 1,
			'zip_fragment_size'		=> 0,
			'zip_read_chunk'		=> 1,
			'zip_threshold'			=> 100,
			'log_level'				=> 0,
			'ftp_port'				=> 21,
			'ftp_passive'			=> 1,
			'sftp_port'				=> 22,
		);

		$this->setData('data', $data);
	}

	/**
	 * Get profiles array
	 */
	public function getProfilesArray() {
		$collection = Mage::getModel('magebackup/profile')
			->getCollection()
		;

		$options	= array();
		foreach ($collection as $profile) {
			$options[$profile->getId()]	= $profile->getName();
		}

		return $options;
	}

	/////////////////////////////////////////////////////////////////

	public function getOutputDir() {
		if (empty($this->outputDir)) {
			$this->outputDir	= Mage::helper('magebackup')->cleanPath(str_replace('{{base_dir}}', Mage::getBaseDir(), $this->getValue('output_directory', '{{base_dir}}/var/magebackup')));
		}

		return $this->outputDir;
	}

	public function getTmpDir() {
		if (empty($this->tmpDir)) {
			$this->tmpDir	= Mage::helper('magebackup')->cleanPath($this->getOutputDir() . '/tmp');
		}

		return $this->tmpDir;
	}

	public function getLogDir() {
		if (empty($this->logDir)) {
			$this->logDir	= Mage::helper('magebackup')->cleanPath($this->getOutputDir() . '/log');
		}

		return $this->logDir;
	}

	public function genArchiveName() {
		if (empty($this->archiveName)) {
			$this->archiveName	= sprintf('site-%s-%s-%s', isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $this->getValue('server_name'), strftime('%Y%m%d'), strftime('%H%M%S')) . '.zip';
		}

		return $this->archiveName;
	}

	protected function _processArray($array) {
		asort($array);

		$result	= array();
		$last	= null;

		foreach ($array as $item) {
			$item	= trim($item);

			if ($item == '' || strpos($item, $last . '/') === 0) {
				continue;
			} else {
				$last		= $item;
				$result[]	= $item;
			}
		}

		return $result;
	}

	public function getExcludedFiles() {
		if (!$this->excludedFiles) {
			$length					= strlen(Mage::getBaseDir() . '/');
			$excludedFiles			= $this->getValue('excluded_files');
			$excludedFiles			= explode(',', $excludedFiles);
			$excludedFiles[]		= rtrim(substr($this->getOutputDir(), $length), '\\/');
			$excludedFiles[]		= 'var/backups';
			$excludedFiles[]		= 'var/magebackup';
			$excludedFiles			= $this->_processArray($excludedFiles);

			$this->excludedFiles	= $excludedFiles;
		}

		return $this->excludedFiles;
	}

	public function getSkippedDirs() {
		if (!$this->skippedDirs) {
			$skippedDirs			= $this->getValue('skipped_dirs');
			$skippedDirs			= explode(',', $skippedDirs);
			$skippedDirs[]			= 'var/cache';
			$skippedDirs[]			= 'var/report';
			$skippedDirs			= $this->_processArray($skippedDirs);

			$this->skippedDirs		= $skippedDirs;
		}

		return $this->skippedDirs;
	}

	public function getSkippedFiles() {
		if (!$this->skippedFiles) {
			$skippedFiles			= $this->getValue('skipped_files');
			$skippedFiles			= explode(',', $skippedFiles);
			$skippedFiles[]			= 'var/cache';
			$skippedFiles[]			= 'var/report';
			$skippedFiles			= $this->_processArray($skippedFiles);

			$this->skippedFiles	= $skippedFiles;
		}

		return $this->skippedFiles;
	}

	public function getExcludedTables() {
		if (!$this->excludedTables) {
			$excludedTables			= $this->getValue('excluded_tables');
			$excludedTables			= explode(',', $excludedTables);
			$excludedTables			= $this->_processArray($excludedTables);

			$this->excludedTables	= $excludedTables;
		}

		return $this->excludedTables;
	}

	public function getSkippedTables() {
		if (!$this->skippedTables) {
			$skippedTables			= $this->getValue('skipped_tables');
			$skippedTables			= explode(',', $skippedTables);
			$skippedTables			= $this->_processArray($skippedTables);

			$this->skippedTables	= $skippedTables;
		}

		return $this->skippedTables;
	}

	public function getIncludedDatabases() {
		if (!$this->includedDatabases) {
			$includedDatabases			= $this->getValue('included_databases');
			$includedDatabases			= explode(',', $includedDatabases);
			$includedDatabases			= $this->_processArray($includedDatabases);

			$this->includedDatabases	= $includedDatabases;
		}

		return $this->includedDatabases;
	}
}