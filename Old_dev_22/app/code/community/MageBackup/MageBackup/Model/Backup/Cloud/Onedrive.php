<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup cloud OneDrive model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Cloud_OneDrive extends MageBackup_MageBackup_Model_Backup_Cloud {
	const CONFIG	= 'eyJjbGllbnRfaWQiOiI1MjI5MDZlMi1iMDcyLTQyNTctYWI5NC1lM2EwN2E2NzUzODYiLCJjbGllbnRfc2VjcmV0IjoiQ2Zxa3M3M0J6V3JKMTdIUUZ1NnBIaloiLCJyZWRpcmVjdF91cmkiOiJodHRwczpcL1wvbWFnZWJhY2t1cC5jb21cL29hdXRoMlwvb25lZHJpdmUucGhwIn0=';

	/** @var  \MageBackup\OneDrive\Client */
	protected $client;

	public static function getConfig() {
		return json_decode(base64_decode(self::CONFIG));
	}

	protected function getClient() {
		if (!$this->client) {
			require_once Mage::getBaseDir('lib') . '/MageBackup/OneDrive/OneDrive.php';

			$profile		= $this->backup->getProfile();
			$log			= $this->backup->getLog();
			$accessToken	= $profile->getValue('onedrive_access_token');
			$refreshToken	= $profile->getValue('onedrive_refresh_token');
			$redirectUri	= $profile->getValue('onedrive_redirect_uri');
			$app			= self::getConfig();


			$client	= new MageBackup\OneDrive\OneDrive(array(
				'client_id'		=> $app->client_id,
				'client_secret'	=> $app->client_secret,
				'access_token'	=> $accessToken,
				'refresh_token'	=> $refreshToken,
				'redirect_uri'	=> $redirectUri,
			));

			if ($client->isAccessTokenExpired()) {
				$token	= $client->refreshToken($client->getRefreshToken());
				
				Mage::getSingleton('magebackup/profile_cloud_onedrive')->saveAccessToken($this->backup->getProfile()->getId(), array(
					'access_token'	=> $token->access_token,
					'refresh_token'	=> $token->refresh_token
				));
			}

			$this->client	= $client;
		}

		return $this->client;
	}

	protected function uploadPart($file) {
		if (!$this->getClient()) {
			return false;
		}

		$profile	= $this->backup->getProfile();
		$log		= $this->backup->getLog();
		$directory	= $profile->getValue('onedrive_directory');
		$path		= Mage::helper('magebackup')->cleanPath($directory . '/' . basename($file));
		$response	= $this->client->upload($path, $file);

		return $response;
	}
}