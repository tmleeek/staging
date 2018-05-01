<?php
class Extendware_EWImageOpt_Model_Override_Mage_Core_Design_Package extends Extendware_EWImageOpt_Model_Override_Mage_Core_Design_Package_Bridge
{
	static protected $checkFilemtime = null;
	static protected $cacheImages = null;
    protected $_config = null;
    protected $_fallback = null;
    
	public function __construct() {
		if (self::$checkFilemtime === null) self::$checkFilemtime = Mage::helper('ewimageopt/config')->isFilemtimeEnabled();
		if (self::$cacheImages === null) self::$cacheImages = Mage::helper('ewimageopt/config')->isSkinImageCacheEnabled();
		self::createHtaccessFile();
		if (@class_exists('Mage_Core_Model_Design_Config', true) === true) {
			if (is_null($this->_config)) {
	            $this->_config = Mage::getSingleton('core/design_config');
	        }
	        if (is_null($this->_fallback)) {
	            $this->_fallback = Mage::getSingleton('core/design_fallback', array(
	                'config' => $this->_config,
	            ));
	        }	
		}
	}
	
	public function setArea($area)
    {
    	parent::setArea($area);
    	if ($area == 'adminhtml') {
    		self::$cacheImages = false;
    	}
        return $this;
    }
    
	public function getSkinUrl($file = null, array $params = array())
    {
    	$fileExtension = pathinfo($file, PATHINFO_EXTENSION);
    	if (self::$cacheImages === false or in_array($fileExtension, array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'svg')) === false) {
    		return parent::getSkinUrl($file, $params);
    	}
    
    	Varien_Profiler::start(__METHOD__);
        if (empty($params['_type'])) {
            $params['_type'] = 'skin';
        }
        if (empty($params['_default'])) {
            $params['_default'] = false;
        }
        $this->updateParamDefaults($params);
        if (!empty($file)) {
            $result = $this->_fallback($file, $params, array(
                array(),
                array('_theme' => $this->getFallbackTheme()),
                array('_theme' => self::DEFAULT_THEME),
            ));
        }

        $sourceFile = $this->getSkinBaseFile($params) . (empty($file) ? '' : $file);
        if (file_exists($sourceFile) === false) return parent::getSkinUrl($file, $params);
        
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
        $result = $this->getBaseUrl() . Mage::helper('ewimageopt/config')->getSlugCustomPath() . '/media/skin/' . str_replace(DS, '/', $path);
		
        Varien_Profiler::stop(__METHOD__);
        return $result;
    }
    
    public function getBaseUrl() {
    	static $baseUrl = null;
    	if ($baseUrl === null) {
    		$baseUrl = Mage::helper('ewimageopt')->rewriteUrl('skin_image', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA));
    	}
    	
    	return $baseUrl;
    }
	public function getSkinBaseFile(array $params=array())
    {
        $params['_type'] = 'skin';
        $this->updateParamDefaults($params);
        $baseUrl = BP . DS . 'skin' . DS
            .$params['_area'].DS.$params['_package'].DS.$params['_theme'].DS;
        return $baseUrl;
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
			$directory = Mage::getConfig()->getOptions()->getMediaDir() . DS . Mage::helper('ewimageopt/config')->getSlugCustomPath() . DS . 'media' . DS . 'skin';
			if (Mage::getConfig()->getOptions()->createDirIfNotExists($directory) === false) {
	    		Mage::throwException(Mage::helper('ewimageopt')->__('Could not create directory %s', $directory));
	    	}
		}
		
    	return $directory;
	}
}