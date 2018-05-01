<?php
class Extendware_EWImageOpt_Model_Override_Mage_Core_Email_Template_Filter extends Extendware_EWImageOpt_Model_Override_Mage_Core_Email_Template_Filter_Bridge
{
	static protected $checkFilemtime = null;
	static protected $cacheImages = null;
	
	public function __construct() {
		if (self::$checkFilemtime === null) self::$checkFilemtime = Mage::helper('ewimageopt/config')->isFilemtimeEnabled();
		if (self::$cacheImages === null) self::$cacheImages = Mage::helper('ewimageopt/config')->isTemplateImageCacheEnabled();
		self::createHtaccessFile();
	}
	
	static protected function createHtaccessFile()
	{
		$cacheDirectory = self::getCacheMediaDir();
		$destFile = $cacheDirectory . DS . '.htaccess';
		if (is_file($destFile) === false or self::$checkFilemtime === true) {
		    $srcFile = Mage::getModuleDir('', 'Extendware_EWImageOpt') . DS . 'resources' . DS . '.htaccess.template';
			if (@filemtime($srcFile) > @filemtime($destFile)) {
				@copy($srcFile, $destFile);
			}
		}
	}
	
	static protected function getCacheMediaDir() {
		static $directory = null;
		if ($directory === null) {
			$directory = Mage::getConfig()->getOptions()->getMediaDir() . DS . Mage::helper('ewimageopt/config')->getSlugCustomPath() . DS . 'media' . DS . 'template';
			if (Mage::getConfig()->getOptions()->createDirIfNotExists($directory) === false) {
	    		Mage::throwException(Mage::helper('ewimageopt')->__('Could not create directory %s', $directory));
	    	}
		}
		
    	return $directory;
	}
	
	public function mediaDirective($construction)
    {
    	if (!$this instanceof Mage_Widget_Model_Template_Filter) {
    		return parent::mediaDirective($construction);
    	}
    	
    	if (self::$cacheImages === true) {
    		$params = $this->_getIncludeParameters($construction[2]);
    		
	    	$sourceFile = Mage::getConfig()->getOptions()->getMediaDir() . DS . $params['url'];
	        if (file_exists($sourceFile) === false) return parent::mediaDirective($construction);
	        
	        $fileName = substr(md5($sourceFile), 0, 6);
			$file = self::getCacheMediaDir() . DS . dechex(ceil(hexdec($fileName[0].$fileName[1].$fileName[2])/16)) . DS . $fileName[3] . DS . pathinfo($sourceFile, PATHINFO_FILENAME) . '.' . pathinfo($sourceFile, PATHINFO_EXTENSION);
	        if (self::$checkFilemtime === true) {
	        	if (@filemtime($sourceFile) >= @filemtime($file)) {
	        		if (@filemtime($file) > 0) @unlink($file);
	        	}
	        }
	        
	        if (file_exists($file) === false) {
	        	if (!is_dir(dirname($file))) Extendware_EWCore_Helper_File::mkdir(dirname($file));
	        	copy($sourceFile, $file);
		    	if (Mage::helper('ewimageopt/config')->isSkinImageOptimizingEnabled() === true) {
		   			if (Mage::helper('ewimageopt/config')->getOptimizerMode() == 'realtime') {
		   				if (Mage::helper('ewimageopt')->lock('optimizer.lock') === true) {
		    				Mage::helper('ewimageopt')->optimizeImage($file, false);
		    				Mage::helper('ewimageopt')->unlock('optimizer.lock');
		   				}
		   			}
		    	}
	        }
	        
	        $baseDir = self::getCacheMediaDir();
	        $path = str_replace($baseDir . DS, '', $file);
	        return $this->getBaseUrl() . Mage::helper('ewimageopt/config')->getSlugCustomPath() . '/media/template/' . str_replace(DS, '/', $path);
    	} 
    	
    	return parent::mediaDirective($construction);
    }
    
	public function getBaseUrl() {
    	static $baseUrl = null;
    	if ($baseUrl === null) {
    		$baseUrl = Mage::helper('ewimageopt')->rewriteUrl('skin_image', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA));
    	}
    	
    	return $baseUrl;
    }
}