<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup cloud FTP model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Cloud_FTP extends MageBackup_MageBackup_Model_Backup_Cloud {
	protected $connection;

	protected function getConnection() {
		if (!$this->connection) {
			$profile	= $this->backup->getProfile();
			$log		= $this->backup->getLog();
			$host		= $profile->getValue('ftp_server');
			$port		= $profile->getValue('ftp_port', 21);
			$username	= $profile->getValue('ftp_username');
			$password	= $profile->getValue('ftp_password');
			$passive	= $profile->getValue('ftp_passive', 1);
			$ftps		= $profile->getValue('ftp_ftps', 0);

			$connection	= $ftps ? ftp_ssl_connect($host, $port) : ftp_connect($host, $port);

			if ($connection) {
				$result	= ftp_login($connection, $username, $password);

				if ($result) {
					ftp_pasv($connection, (bool) $passive);

					$this->connection	= $connection;
				} else {
					$log->error('Could not login to FTP server');
					return false;
				}
			} else {
				$log->error('Could not connect to FTP server');
				return false;
			}
		}

		return $this->connection;
	}

	protected function uploadPart($file) {
		if (!$this->getConnection()) {
			return false;
		}

		$profile	= $this->backup->getProfile();
		$log		= $this->backup->getLog();
		$path		= $profile->getValue('ftp_path');
		$path		= Mage::helper('magebackup')->cleanPath($path . '/' . basename($file));

		if (ftp_put($this->connection, $path, $file, FTP_BINARY)) {
			return true;
		} else {
			$log->error('FTP upload failed');
			return false;
		}
	}
}