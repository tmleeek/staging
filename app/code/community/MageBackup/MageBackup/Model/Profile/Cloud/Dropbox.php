<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup profile cloud dropbox model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Profile_Cloud_Dropbox {

	public function oauthOpen() {
		require_once Mage::getBaseDir('lib') . '/MageBackup/Dropbox/autoload.php';

		$class			= Mage::getConfig()->getModelClassName('magebackup/backup_cloud_dropbox');
		$app			= $class::getConfig();
		$client			= new MageBackup\Dropbox\DropboxApp($app->key, $app->secret);

		$dropbox		= new MageBackup\Dropbox\Dropbox($client);
		$authHelper		= $dropbox->getAuthHelper();

		$authUrl		= $authHelper->getAuthUrl();

		return $authUrl;
	}

	public function getAuth($code) {
		require_once Mage::getBaseDir('lib') . '/MageBackup/Dropbox/autoload.php';

		$class			= Mage::getConfig()->getModelClassName('magebackup/backup_cloud_dropbox');
		$app			= $class::getConfig();
		$client			= new MageBackup\Dropbox\DropboxApp($app->key, $app->secret);

		$dropbox		= new MageBackup\Dropbox\Dropbox($client);
		$authHelper		= $dropbox->getAuthHelper();

		try {
			$accessToken	= $authHelper->getAccessToken($code);

			return (object) array(
				'error'			=> false,
				'access_token'	=> $accessToken->getToken()
			);
		} catch (Exception $e) {
			return (object) array(
				'error'			=> 1,
				'access_token'	=> $e->getMessage()
			);
		}
	}
}