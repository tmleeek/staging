<?php
/**
 * created : 22 septembre 2009
 * 
 * @category SQLI
 * @package Sqli_Video
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Video
 */
class Tatva_Video_Model_Video extends Mage_Core_Model_Abstract
{
	/**
	 * Log file name
	 * @static string
	 */
	static $LOG_FILE = "video";

	/**
	 * Logger
	 * @var Zend_Log
	 */
	private $logger;

 	/**
	 * Log priority
	 * @static string
	 */
	static $LOG_PRIORITY = Zend_Log::DEBUG;

	/**
	 * Taille des pages de la collection de produit
	 * @static int
	 */
	static $PAGE_SIZE = 50;


	protected function init() {
		if (! $this->isActive ())
			return false;
		ini_set ( 'memory_limit', '1024M' );
		$this->constructLogger ( self::$LOG_FILE, self::$LOG_PRIORITY );
		$this->getLogger ()->log ( "-------------------------------------------", Zend_log::INFO );
		return true;
	}

	/**
	 * Mise à jour des stocks
	 */
   

	protected function constructLogger($logFile, $priority) {
		$logFile = Mage::getStoreConfig ( 'system/filesystem/var' ) . "/log/$logFile" . "_" . date ( 'Ymd' ) . ".log";
		$logger = new Zend_Log ( );
		$writer = new Zend_Log_Writer_Stream ( $logFile );
		$format = '%timestamp% %priorityName% : %message%' . PHP_EOL;
		$formatter = new Zend_Log_Formatter_Simple ( $format );
		$writer->setFormatter ( $formatter );
		$logger->addWriter ( $writer );
		$filter = new Zend_Log_Filter_Priority ( $priority );
		$logger->addFilter ( $filter );
		$this->logger = $logger;
	}

	public function getLogger() {
		return $this->logger;
	}

}

?>