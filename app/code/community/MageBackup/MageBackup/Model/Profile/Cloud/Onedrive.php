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
class MageBackup_MageBackup_Model_Profile_Cloud_OneDrive {

	public function oauthOpen() {
		require_once Mage::getBaseDir('lib') . '/MageBackup/OneDrive/OneDrive.php';

		$class	= Mage::getConfig()->getModelClassName('magebackup/backup_cloud_onedrive');
		$app	= $class::getConfig();

		$client	= new MageBackup\OneDrive\OneDrive(array(
			'client_id'		=> $app->client_id,
			'client_secret'	=> $app->client_secret,
		));

		$redirectUri	= $app->redirect_uri . '?return=' . Mage::helper('adminhtml')->getUrl('adminhtml/magebackup_profile/oneDriveAuth2/');

		$authUrl		= $client->createAuthUrl(array(
			'wl.offline_access',
			'files.readwrite',
			'onedrive.readwrite',
		), $redirectUri);

		$session	= Mage::getSingleton('magebackup/session');
		$session->setData('onedrive.redirect_uri', $redirectUri);

		return $authUrl;
	}

	public function getAuth($authCode) {
		require_once Mage::getBaseDir('lib') . '/MageBackup/OneDrive/OneDrive.php';

		$session		= Mage::getSingleton('magebackup/session');
		$redirectUri	= $session->getData('onedrive.redirect_uri');

		$class			= Mage::getConfig()->getModelClassName('magebackup/backup_cloud_onedrive');
		$app			= $class::getConfig();

		$client	= new MageBackup\OneDrive\OneDrive(array(
			'client_id'		=> $app->client_id,
			'client_secret'	=> $app->client_secret,
			'redirect_uri'	=> $redirectUri,
		));

		$client->authenticate($authCode, $redirectUri);

		if ($client->isAccessTokenExpired()) {
			$client->refreshToken($client->getRefreshToken());
		}

		return (object) array(
			'access_token'	=> $client->getAccessToken(),
			'refresh_token'	=> $client->getRefreshToken(),
			'redirect_uri'	=> $redirectUri
		);
	}

	public function saveAccessToken($profileId, $values) {
		$keys		= array(
			'onedrive_access_token',
			'onedrive_refresh_token'
		);

		foreach ($keys as $key) {
			$vkey	= substr($key, 9);
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