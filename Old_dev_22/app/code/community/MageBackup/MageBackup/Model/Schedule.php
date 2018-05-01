<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Main observer
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Schedule extends Mage_Core_Model_Abstract
{

	public function databasebackup()
    {
		try
        {
			$jobid			= '2';

			/** @var MageBackup_MageBackup_Model_Profile $profile */
			$profile	= Mage::getModel('magebackup/profile')->load($jobid);
			/** @var MageBackup_MageBackup_Model_Backup $backup */
			$backup		= Mage::getModel('magebackup/backup');

			$fileName	= $profile->genArchiveName();

			$backup->setId(null)
				->setName(Mage::helper('magebackup')->__('[CRON] Backup taken on %s', date('j F Y G:i:s')))
				->setDescription(Mage::helper('magebackup')->__('Cron backup taken on %s', date('j F Y G:i:s')))
				->setStartTime(Mage::getSingleton('core/date')->gmtDate())
				->setProfileId($profile->getId())
				->setFileName($fileName)
				->setFilePath($profile->getOutputDir() . '/' . $fileName)
				->setStatus('fail');
            /*echo "<pre>";
            print_r($backup->getData());
            exit;*/
			$backup->save();

			Mage::helper('magebackup')->unlimit();

			$backup->backup();

		} catch (Exception $e) {

		}
	}

    public function filesbackup()
    {
		try
        {
			$jobid			= '1';

			/** @var MageBackup_MageBackup_Model_Profile $profile */
			$profile	= Mage::getModel('magebackup/profile')->load($jobid);
			/** @var MageBackup_MageBackup_Model_Backup $backup */
			$backup		= Mage::getModel('magebackup/backup');

			$fileName	= $profile->genArchiveName();

			$backup->setId(null)
				->setName(Mage::helper('magebackup')->__('[CRON] Backup taken on %s', date('j F Y G:i:s')))
				->setDescription(Mage::helper('magebackup')->__('Cron backup taken on %s', date('j F Y G:i:s')))
				->setStartTime(Mage::getSingleton('core/date')->gmtDate())
				->setProfileId($profile->getId())
				->setFileName($fileName)
				->setFilePath($profile->getOutputDir() . '/' . $fileName)
				->setStatus('fail');
            /*echo "<pre>";
            print_r($backup->getData());
            exit;*/
			$backup->save();

			Mage::helper('magebackup')->unlimit();

			$backup->backup();

		} catch (Exception $e) {

		}
	}

    public function mediabackup()
    {
		try
        {
			$jobid			= '9';

			/** @var MageBackup_MageBackup_Model_Profile $profile */
			$profile	= Mage::getModel('magebackup/profile')->load($jobid);
			/** @var MageBackup_MageBackup_Model_Backup $backup */
			$backup		= Mage::getModel('magebackup/backup');

			$fileName	= $profile->genArchiveName();

			$backup->setId(null)
				->setName(Mage::helper('magebackup')->__('[CRON] Backup taken on %s', date('j F Y G:i:s')))
				->setDescription(Mage::helper('magebackup')->__('Cron backup taken on %s', date('j F Y G:i:s')))
				->setStartTime(Mage::getSingleton('core/date')->gmtDate())
				->setProfileId($profile->getId())
				->setFileName($fileName)
				->setFilePath($profile->getOutputDir() . '/' . $fileName)
				->setStatus('fail');
            /*echo "<pre>";
            print_r($backup->getData());
            exit;*/
			$backup->save();

			Mage::helper('magebackup')->unlimit();

			$backup->backup();

		} catch (Exception $e) {

		}
	}
}
