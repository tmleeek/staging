<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile cloud model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Profile_Cloud {
	const ENGINE_NONE			= 0;
	const ENGINE_AMAZON_S3		= 's3';
	const ENGINE_AMAZON_GLACIER	= 'glacier';
	const ENGINE_GOOGLE_STORAGE	= 'googlestorage';
	const ENGINE_GOOGLE_DRIVE	= 'googledrive';
	const ENGINE_ONE_DRIVE		= 'onedrive';
	const ENGINE_DROPBOX		= 'dropbox';
	const ENGINE_FTP			= 'ftp';
	const ENGINE_SFTP			= 'sftp';

	public function getEnginesArray() {
		$options	= array(
			self::ENGINE_NONE			=> Mage::helper('magebackup')->__('None'),
			self::ENGINE_AMAZON_S3		=> Mage::helper('magebackup')->__('Upload to Amazon S3'),
			self::ENGINE_AMAZON_GLACIER	=> Mage::helper('magebackup')->__('Upload to Amazon Glacier'),
			self::ENGINE_GOOGLE_STORAGE	=> Mage::helper('magebackup')->__('Upload to Google Cloud Storage'),
			self::ENGINE_GOOGLE_DRIVE	=> Mage::helper('magebackup')->__('Upload to Google Drive'),
			self::ENGINE_ONE_DRIVE		=> Mage::helper('magebackup')->__('Upload to Microsoft OneDrive'),
			self::ENGINE_DROPBOX		=> Mage::helper('magebackup')->__('Upload to Dropbox'),
			self::ENGINE_FTP			=> Mage::helper('magebackup')->__('Upload to Remote FTP Server'),
			self::ENGINE_SFTP			=> Mage::helper('magebackup')->__('Upload to Remote SFTP Server'),
		);

		return $options;
	}
}