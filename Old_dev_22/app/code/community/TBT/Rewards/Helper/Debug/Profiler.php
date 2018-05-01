<?php
/**
 * Pretty much this is a wrapper for Varien_Profiler
 * @category   TBT
 * @package    TBT_Rewards
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewards_Helper_Debug_Profiler extends TBT_Rewards_Helper_Debug {
	/**
	 * 
	 * @param string $timerName
	 * @param string $key			key from the timer entry.  default is 'sum' which will return an integer
	 * @return mixed depending on $key
	 */
	public function fetch($timerName, $key='sum') {
	    return Varien_Profiler::fetch($timerName, $key);
	}
	
	protected function _profillerLoggingEnabled() {
	    return true;
	}
	
	static $startTimes = array();
	
	/**
	 * Start the profiler for given bucket
	 *
	 * @param string $bucketName
	 * @return TBT_DataPump_Helper_AjaxProfiler
	 */
	public function start($bucketName)
	{
	    self::$startTimes[$bucketName] = self::microtime_float();	    
	    Varien_Profiler::start($bucketName);
	    return $this;
	}
	
	/**
	 * Stop the profiler for given bucket.
	 * Will also log an html file with the $bucketName and timestamp to Mage log directory.
	 *
	 * @param string $bucketName
	 * @return TBT_DataPump_Helper_AjaxProfiler
	 */
	public function stop($bucketName)
	{
	    Varien_Profiler::stop($bucketName);
	    
	    if($this->_profillerLoggingEnabled()) {
	        $this->notice("Profiler: " . $bucketName . " total time: ". $this->fetch($bucketName));
	    }
	    
	    $this->_saveLog($bucketName);
	    return $this;
	}
	
	/**
	 * Will initiate saving of the html log file. Variables are stored here.
	 *
	 * @param string $bucketName
	 * @return TBT_DataPump_Helper_AjaxProfiler
	 */
	protected function _saveLog($bucketName)
	{
	    $timeStamp = date('d.m.Y_H.i.s');
	    $duration = -1;
	    if (isset(self::$startTimes[$bucketName])){
	        $duration = self::microtime_float() - self::$startTimes[$bucketName];
	        unset(self::$startTimes[$bucketName]);
	    }
	
	    $file = "st_profiler_{$bucketName}_{$timeStamp}.html";
	    $logDir = Mage::getBaseDir('var') . DS . 'log';
	    $logFile = $logDir . DS . $file;
	
	    $this->saveToFile($this->_getHtml(), $logDir, $file);
	    Mage::log("ST_AjaxProfiler for {$bucketName} took {$duration} seconds. Details were saved in {$logFile}");
	    return $this;
	}
	
	/**
	 * Will generate an HTML string containing log info.
	 * This was hacked together to make it work as fast as possible. Sorry for the mess.
	 *
	 * @param string $bucketName
	 * @return TBT_DataPump_Helper_AjaxProfiler
	 */
	protected function _getHtml()
	{
	    $block = Mage::getBlockSingleton('core/profiler');
	
	    if (!$block){
	        $layout = Mage::getSingleton('core/layout');
	        $block = $layout->createBlock('core/profiler');
	
	    }
	
	    if (!$block){
	        return "aoe_profiler may not be installed properly.";
	    }
	
	    $html = "";
	    $html .= "<html>";
	    $html .= "    <head>";
	    // $html .= "        <script type=\"text/javascript\" src=\"" . Mage::getBaseUrl('js') . "/prototype/prototype.js\"></script>";
	    $html .= "    </head>";
	    $html .= "    <body>";
	    $html .= "        <h3>";
	    $html .= "            " . date('F j, Y h:i:s A') . ": <br/>" . Mage::helper('core/url')->getCurrentUrl();
	    $html .= "        </h3>";
	    $html .= "        <br />";
	    $html .=          $block->toHtml();
	    $html .= "    </body>";
	    $html .= "</html>";
	    $html .= "<!-- end of profiler -->";
	
	    return $html;
	}
	
	/**
	 * Will save message contents to a file.
	 * I copied this from Mage::log with some changes. This was hacked together to make it work as fast as possible. Sorry for the mess.
	 *
	 * @param string $message
	 * @param string $logDir
	 * @param string $file
	 */
	protected function saveToFile($message, $logDir = null, $file = "profiler_noName.html")
	{
	    try
	    {
	        if (empty($logDir))
	        {
	            $logDir  = Mage::getBaseDir('var') . DS . 'log';
	        }
	
	        $logFile = $logDir . DS . $file;
	
	        if (!is_dir($logDir)) {
	            mkdir($logDir);
	            chmod($logDir, 0777);
	        }
	
	        if (!file_exists($logFile)) {
	            file_put_contents($logFile, '');
	            chmod($logFile, 0777);
	        }
	
	        $format = '%message%' . PHP_EOL;
	        $formatter = new Zend_Log_Formatter_Simple($format);
	        $writerModel = (string)Mage::getConfig()->getNode('global/log/core/writer_model');
	        if (!$writerModel) {
	            $writer = new Zend_Log_Writer_Stream($logFile);
	        } else {
	            $writer = new $writerModel($logFile);
	        }
	        $writer->setFormatter($formatter);
	
	        $log = new Zend_Log($writer);
	        $log->log($message, Zend_Log::DEBUG);
	
	    } catch (Exception $e) {
	        Mage::logException($e);
	    }
	}
	
	protected function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}	
	
}
