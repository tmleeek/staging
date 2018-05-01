<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup cloud Amazon S3 model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Cloud_S3 extends MageBackup_MageBackup_Model_Backup_Cloud {
	protected $client;

	protected function getClient() {
		if (!$this->client) {
			$profile		= $this->backup->getProfile();
			$log			= $this->backup->getLog();
			$accessKey		= $profile->getValue('s3_accesskey');
			$secretKey		= $profile->getValue('s3_secretkey');
			$region			= $profile->getValue('s3_region');
			$useSSL			= $profile->getValue('s3_use_ssl');
			$useAWS2		= $profile->getValue('s3_use_aws2');

			if (!$accessKey || !$secretKey) {
				throw new Exception('Amazon S3 is not configured');
			}

			if ($useAWS2) {
				require_once Mage::getBaseDir('lib') . '/MageBackup/Aws2/aws-autoloader.php';

				$client	= MageBackup\Aws\S3\S3Client::factory(array(
					'credentials'	=> array(
						'key'		=> $accessKey,
						'secret'	=> $secretKey,
					),
					'region'	=> $region,
					'scheme'	=> $useSSL ? 'https' : 'http',
					'version'	=> 'latest',
					'http'		=> array(
						'verify'	=> Mage::getBaseDir('lib') . '/MageBackup/cacert.pem'
					)
				));
			} else {
				require_once Mage::getBaseDir('lib') . '/MageBackup/Aws/aws-autoloader.php';

				$client	= new MageBackup\Aws\S3\S3Client(array(
					'credentials'	=> array(
						'key'		=> $accessKey,
						'secret'	=> $secretKey,
					),
					'region'	=> $region,
					'scheme'	=> $useSSL ? 'https' : 'http',
					'version'	=> 'latest',
					'http'		=> array(
						'verify'	=> Mage::getBaseDir('lib') . '/MageBackup/cacert.pem'
					)
				));
			}

			$this->client	= $client;
		}

		return $this->client;
	}

	protected function uploadPart($file) {
		$client			= $this->getClient();
		$profile		= $this->backup->getProfile();
		$bucket			= $profile->getValue('s3_bucket');
		$directory		= $profile->getValue('s3_directory');
		$path			= Mage::helper('magebackup')->cleanPath($directory . '/' . basename($file));

		$storageClass	= $profile->getValue('s3_storage_class');

		if (!array_key_exists($storageClass, Mage::getSingleton('magebackup/profile_cloud_amazon')->getStorageClassesArray())) {
			$storageClass	= 'STANDARD';
		}

		return $client->putObject(array(
			'Bucket'		=> $bucket,
			'Key'			=> $path,
			'SourceFile'	=> $file,
			'ACL'			=> 'private',
			'StorageClass'	=> $storageClass,
		));
	}
}