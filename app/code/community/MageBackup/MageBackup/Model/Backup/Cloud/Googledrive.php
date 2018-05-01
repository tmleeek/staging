<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup cloud Google Drive model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Cloud_GoogleDrive extends MageBackup_MageBackup_Model_Backup_Cloud {
	const CONFIG	= 'eyJpbnN0YWxsZWQiOnsiY2xpZW50X2lkIjoiODkzOTM5NDk4NTMwLW40YWk0aTQzNWFyYnMwZjB2bGdlc21rcG1jOHI5dXNxLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwicHJvamVjdF9pZCI6Im1hZ2ViYWNrdXAtMTMxNiIsImF1dGhfdXJpIjoiaHR0cHM6Ly9hY2NvdW50cy5nb29nbGUuY29tL28vb2F1dGgyL2F1dGgiLCJ0b2tlbl91cmkiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20vby9vYXV0aDIvdG9rZW4iLCJhdXRoX3Byb3ZpZGVyX3g1MDlfY2VydF91cmwiOiJodHRwczovL3d3dy5nb29nbGVhcGlzLmNvbS9vYXV0aDIvdjEvY2VydHMiLCJjbGllbnRfc2VjcmV0IjoiQk9FeTl2QldXV0Y1ZF9aME1nM2tWS29wIiwicmVkaXJlY3RfdXJpcyI6WyJ1cm46aWV0Zjp3ZzpvYXV0aDoyLjA6b29iIiwiaHR0cDovL2xvY2FsaG9zdCJdfX0=';

	/** @var  Google_Client */
	protected $client;

	/** @var  Google_Service_Drive */
	protected $service;

	public static function getConfig() {
		return base64_decode(self::CONFIG);
	}

	protected function getService() {
		if (!$this->service) {
			require_once Mage::getBaseDir('lib') . '/MageBackup/Google/autoload.php';

			$profile		= $this->backup->getProfile();
			$log			= $this->backup->getLog();
			$accessToken	= $profile->getValue('googledrive_access_token');
			$refreshToken	= $profile->getValue('googledrive_refresh_token');

			if (!$accessToken) {
				$log->error('Google Drive access token was not configured');
				return false;
			}

			$client	= new Google_Client();
			$client->setApplicationName('MageBackup');
			$client->setScopes(Google_Service_Drive::DRIVE);
			$client->setAuthConfig(self::getConfig());
			$client->setRedirectUri(Mage::helper('adminhtml')->getUrl('adminhtml/magebackup_profile/googleDriveAuth2/'));
			$client->setAccessType('offline');
			$client->setAccessToken(json_encode(array(
				'access_token'	=> $accessToken,
				'refresh_token'	=> $refreshToken
			)));

			if ($client->isAccessTokenExpired()) {
				$client->refreshToken($client->getRefreshToken());

				$token	= json_decode($client->getAccessToken());

				Mage::getSingleton('magebackup/profile_cloud_googledrive')->saveAccessToken($this->backup->getProfile()->getId(), array(
					'access_token'	=> $token->access_token,
					'refresh_token'	=> $token->refresh_token
				));
			}

			$this->client	= $client;
			$this->service	= new Google_Service_Drive($this->client);
		}

		return $this->service;
	}

	protected function createFolder($parentId, $folder) {
		$metadata	= new Google_Service_Drive_DriveFile(array(
			'name'		=> $folder,
			'mimeType'	=> 'application/vnd.google-apps.folder',
			'parents'	=> array($parentId)
		));

		$file	= $this->service->files->create($metadata, array(
			'fields'	=> 'id'
		));

		return $file->id;
	}

	protected function getFolderId($path, $create = false) {
		$path	= Mage::helper('magebackup')->cleanPath($path);

		if (empty($path)) {
			return 'root';
		}

		$folders	= explode('/', $path);
		$parentId	= 'root';

		foreach ($folders as $folder) {
			$search		= 'name = \'' . str_replace('\'', '\\\'', $folder) . '\' and mimeType = \'application/vnd.google-apps.folder\'';
			$results	= $this->service->files->listFiles(array(
				'q'			=> $search
			));

			if (!empty($files = $results->getFiles())) {
				$parentId	= $files[0]['id'];

				continue;
			}

			if (!$create) {
				return false;
			}

			$parentId	= $this->createFolder($parentId, $folder);
		}

		return $parentId;
	}

	protected function uploadPart($file) {
		if (!$this->getService()) {
			return false;
		}

		$profile	= $this->backup->getProfile();
		$log		= $this->backup->getLog();
		$directory	= $profile->getValue('googledrive_directory');

		$folderId	= $this->getFolderId($directory);

		$size		= filesize($file);
		$mimeType	= 'application/octet-stream';
		$driveFile	= new Google_Service_Drive_DriveFile();

		$driveFile->setName(basename($file));
		$driveFile->setMimeType($mimeType);

		if ($folderId) {
			$driveFile->setParents(array($folderId));
		}

		if ($size < 5242880) {
			$contents	= file_get_contents($file);

			$result		= $this->service->files->create($driveFile, array(
				'data'			=> $contents,
				'mimeType'		=> $mimeType,
				'uploadType'	=> 'media'
			));

			return $result;
		} else {
			$this->client->setDefer(true);

			$chunkSize	= 1048576;
			$request	= $this->service->files->create($driveFile);
			$media		= new Google_Http_MediaFileUpload($this->client, $request, $mimeType, null, true, $chunkSize);

			$this->client->setDefer(false);
			$media->setFileSize($size);

			$status	= false;
			$fp		= @fopen($file, 'rb');

			while (!$status && !@feof($fp)) {
				$chunk	= @fread($fp, $chunkSize);
				$status	= $media->nextChunk($chunk);
			}

			@fclose($fp);

			return $status;
		}
	}
}