<?php
/**
 * @copyright	Copyright (c) 2016 MageBackup (http://www.magebackup.com). All rights reserved.
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MageBackup backup log model.
 *
 * @category	MageBackup
 * @package		MageBackup_MageBackup
 * @author		MageBackup Team <admin@magebackup.com>
 */
class MageBackup_MageBackup_Model_Backup_Log {
	const ERROR		= 'error';
	const WARNING	= 'warning';
	const INFO		= 'info';
	const DEBUG		= 'debug';

	protected $_logLevel;
	protected $_filePath;
	protected $_fp;

	public function getFilePath() {
		if (empty($this->_filePath)) {
			$backup		= Mage::registry('magebackup/backup');

			$this->_filePath	= $backup->getProfile()->getLogDir() . '/magebackup.id.' . $backup->getId() . '.log';
		}

		return $this->_filePath;
	}

	public function getLogLevel() {
		if (empty($this->_logLevel)) {
			$backup	= Mage::registry('magebackup/backup');

			$this->_logLevel	= $backup->getProfile()->getValue('log_level', 0);
		}

		return $this->_logLevel;
	}

	public function errorHandler($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext) {
		$this->error($errorMessage . ' in ' . $errorFile . ' on line ' . $errorLine);
	}

	public function __destruct() {
		if ($this->_fp && is_resource($this->_fp)) {
			fclose($this->_fp);
		}
	}

	public function log($message, $level = self::ERROR) {
		$logLevel	= $this->getLogLevel();

		if ($logLevel == 0) {
			return;
		}

		switch ($level) {
			case self::ERROR:
				$intLevel	= 1;
				break;
			case self::WARNING:
				$intLevel	= 2;
				break;
			case self::INFO:
				$intLevel	= 3;
				break;
			case self::DEBUG:
				$intLevel	= 4;
				break;
			default:
				return;
		}

		if ($logLevel < $intLevel) {
			return;
		}

		// open log file
		if (is_null($this->_fp) || !is_resource($this->_fp)) {
			$this->_fp	= fopen($this->getFilePath(), 'a+');
		}

		if (is_null($this->_fp)) {
			return;
		}

		// replace new lines
		if (!is_string($message)) {
			ob_start();
			var_dump($message);

			$message	= ob_get_contents();
			ob_end_clean();
		}

		$message	= str_replace("\r\n", "\n", $message);
		$message	= str_replace("\r", "\n", $message);

		switch ($level) {
			case self::ERROR:
				$string	= 'ERROR   |';
				break;
			case self::WARNING:
				$string	= 'WARNING |';
				break;
			case self::INFO:
				$string	= 'INFO    |';
				break;
			default:
				$string	= 'DEBUG   |';
				break;
		}

		$string	.= @strftime('%y/%m/%d %H:%M:%S') . '|' . $message . "\r\n";

		fwrite($this->_fp, $string);
	}

	public function error($message) {
		$this->log($message, self::ERROR);
	}

	public function warning($message) {
		$this->log($message, self::WARNING);
	}

	public function info($message) {
		$this->log($message, self::INFO);
	}

	public function debug($message) {
		$this->log($message, self::DEBUG);
	}
}