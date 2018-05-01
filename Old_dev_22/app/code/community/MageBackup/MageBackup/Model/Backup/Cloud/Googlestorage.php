<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup cloud Google Storage model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Cloud_GoogleStorage extends MageBackup_MageBackup_Model_Backup_Cloud {
	protected $connection;

	protected function uploadPart($file) {
		$profile	= $this->backup->getProfile();

		if (!$this->connection) {
			$accessKey	= $profile->getValue('googlestorage_accesskey');
			$secretKey	= $profile->getValue('googlestorage_secretkey');

			$this->connection	= new Zend_Service_Amazon_S3($accessKey, $secretKey);
			$this->connection->setEndpoint('http://commondatastorage.googleapis.com');
		}

		$bucket		= $profile->getValue('googlestorage_bucket');
		$directory	= $profile->getValue('googlestorage_directory');
		$path		= Mage::helper('magebackup')->cleanPath($bucket . '/' . $directory . '/' . basename($file));

		return $this->connection->putFileStream($file, $path, array(
			Zend_Service_Amazon_S3::S3_ACL_HEADER	=> Zend_Service_Amazon_S3::S3_ACL_PRIVATE
		));
	}
}