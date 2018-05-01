<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup cloud SFTP model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Cloud_SFTP extends MageBackup_MageBackup_Model_Backup_Cloud {
	protected $connection;

	protected function getConnection() {
		if (!$this->connection) {
			$profile	= $this->backup->getProfile();
			$log		= $this->backup->getLog();
			$host		= $profile->getValue('sftp_server');
			$port		= $profile->getValue('sftp_port', 21);
			$username	= $profile->getValue('sftp_username');
			$password	= $profile->getValue('sftp_password');
			$pubkey		= $profile->getValue('sftp_passive');
			$privkey	= $profile->getValue('sftp_ftps');
			
			// try to connect using public and private key
			if ($pubkey && $privkey) {
				$connection	= ssh2_connect($host, $port, array('hostkey' => 'ssh-rsa'));

				if (!$connection) {
					$log->error('Could not connect to SFTP server');
					return false;
				}

				if (ssh2_auth_pubkey_file($connection, $username, $pubkey, $privkey, $password)) {
					$this->connection	= $connection;
				} else {
					$log->error('Public Key Authentication Failed');
					return false;
				}
			}

			if (!$this->connection) {
				// connect using user & password
				$connection	= ssh2_connect($host, $port);

				if (!$connection) {
					$log->error('Could not connect to SFTP server');
					return false;
				}

				if (ssh2_auth_password($connection, $username, $password)) {
					$this->connection	= $connection;
				} else {
					$log->error('Could not authenticate access to SFTP server');
					return false;
				}
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
		$path		= $profile->getValue('sftp_path');
		$path		= Mage::helper('magebackup')->cleanPath($path . '/' . basename($file));

		$sftpHandle	= ssh2_sftp($this->connection);

		if ($sftpHandle === false) {
			$log->error('Your SSH server does not allow SFTP connections');
			return false;
		}

		$fp		= @fopen('ssh2.sftp://' . $sftpHandle . $path, 'w');

		if ($fp === false) {
			$log->error('Could not open remote SFTP file ' . $path . ' for writing');
			return false;
		}

		$lfp	= @fopen($file, 'rb');

		if ($lfp === false) {
			$log->error('Could not open local file ' . $file . ' for reading');
			@fclose($fp);

			return false;
		}

		$res	= true;

		while (!feof($lfp) && $res !== false) {
			$buffer	= @fread($lfp, 1048756);
			$res	= @fwrite($fp, $buffer);
		}

		@fclose($fp);
		@fclose($lfp);

		if ($res === false) {
			$log->error('Uploading ' . $file . ' has failed');
			return false;
		}

		return true;
	}
}