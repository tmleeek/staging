<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup cloud Dropbox model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Cloud_Dropbox extends MageBackup_MageBackup_Model_Backup_Cloud {
	const CONFIG	= 'eyJrZXkiOiJmdml6a3dmbjk5ajZ6M3AiLCJzZWNyZXQiOiJ3dHRqbnNsMHQzMHpiaWMifQ==';

	protected $client;

	public static function getConfig() {
		return json_decode(base64_decode(self::CONFIG));
	}

	/**
	 * Get Dropbox API Class.
	 * @return MageBackup\Dropbox\DropboxApp
	 */
	protected function getClient() {
		if (!$this->client) {
			$app			= self::getConfig();
			$profile		= $this->backup->getProfile();
			$access_token	= $profile->getValue('dropbox_access_token');

			require_once Mage::getBaseDir('lib') . '/MageBackup/Dropbox/autoload.php';

			$this->client	= new MageBackup\Dropbox\DropboxApp($app->key, $app->secret, $access_token);
		}

		return $this->client;
	}

	protected function uploadPart($file) {
		$profile	= $this->backup->getProfile();
		$directory	= $profile->getValue('dropbox_directory');

		try {
			$client			= $this->getClient();
			$dropbox		= new MageBackup\Dropbox\Dropbox($client);
			$dropboxFile	= new MageBackup\Dropbox\DropboxFile($file, MageBackup\Dropbox\DropboxFile::MODE_READ);

			$uploadedFile	= $dropbox->upload($dropboxFile, $directory . '/' . basename($file), array('autorename' => true));
		} catch (Exception $e) {
			$this->backup->getLog()->error($e->getMessage());
			return false;
		}

		return true;
	}
}