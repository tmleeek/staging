<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile cloud Google Drive model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Profile_Cloud_GoogleDrive {

	public function oauthOpen() {
		require_once Mage::getBaseDir('lib') . '/MageBackup/Google/autoload.php';
		$class	= Mage::getConfig()->getModelClassName('magebackup/backup_cloud_googledrive');
		$client	= new Google_Client();
		$client->setApplicationName('MageBackup');
		$client->setScopes(Google_Service_Drive::DRIVE);
		$client->setAuthConfig($class::getConfig());
		$client->setRedirectUri(Mage::helper('adminhtml')->getUrl('adminhtml/magebackup_profile/googleDriveAuth2/'));
		$client->setAccessType('offline');

		$authUrl	= $client->createAuthUrl();

		return $authUrl;
	}

	public function getAuth($authCode) {
		require_once Mage::getBaseDir('lib') . '/MageBackup/Google/autoload.php';

		$class	= Mage::getConfig()->getModelClassName('magebackup/backup_cloud_googledrive');
		$client	= new Google_Client();
		$client->setApplicationName('MageBackup');
		$client->setScopes(Google_Service_Drive::DRIVE);
		$client->setAuthConfig($class::getConfig());
		$client->setRedirectUri(Mage::helper('adminhtml')->getUrl('adminhtml/magebackup_profile/googleDriveAuth2/'));
		$client->setAccessType('offline');

		$accessToken	= $client->authenticate($authCode);
		$client->setAccessToken($accessToken);

		if ($client->isAccessTokenExpired()) {
			$client->refreshToken($client->getRefreshToken());
		}

		return $client->getAccessToken();
	}

	public function saveAccessToken($profileId, $values) {
		$keys		= array(
			'googledrive_access_token',
			'googledrive_refresh_token'
		);

		foreach ($keys as $key) {
			$vkey	= substr($key, 12);
			$value	= isset($values[$vkey]) ? $values[$vkey] : '';

			$dataModel	= Mage::getModel('magebackup/data');

			$dataModel->loadByFields(array(
				'profile_id'	=> $profileId,
				'name'			=> $key
			));

			$dataModel->setProfileId($profileId)
				->setName($key)
				->setValue($value)
				->save()
			;
		}
	}
}