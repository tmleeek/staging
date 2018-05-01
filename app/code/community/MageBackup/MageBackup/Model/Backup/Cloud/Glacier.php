<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup cloud Amazon Glacier model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Cloud_Glacier extends MageBackup_MageBackup_Model_Backup_Cloud {
	protected $client;

	protected function getClient() {
		if (!$this->client) {
			$profile		= $this->backup->getProfile();
			$log			= $this->backup->getLog();
			$accessKey		= $profile->getValue('glacier_accesskey');
			$secretKey		= $profile->getValue('glacier_secretkey');
			$region			= $profile->getValue('glacier_region');
			$useSSL			= $profile->getValue('glacier_use_ssl');
			$useAWS2		= $profile->getValue('glacier_use_aws2');

			if (!$accessKey || !$secretKey) {
				throw new Exception('Amazon Glacier is not configured');
			}

			if ($useAWS2) {
				require_once Mage::getBaseDir('lib') . '/MageBackup/Aws2/aws-autoloader.php';

				$client	= MageBackup\Aws\Glacier\GlacierClient::factory(array(
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

				$client	= new MageBackup\Aws\Glacier\GlacierClient(array(
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
		$client		= $this->getClient();
		$profile	= $this->backup->getProfile();
		$vault		= $profile->getValue('glacier_vault');
		$directory	= $profile->getValue('glacier_directory');
		$path		= Mage::helper('magebackup')->cleanPath($vault);
		$fp			= @fopen($file, 'rb');

		$result		= $client->uploadArchive(array(
			'vaultName'	=> $path,
			'body'		=> $fp,
		));

		fclose($fp);

		return $result;
	}
}