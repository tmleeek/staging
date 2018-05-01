<?php

class Extendware_EWImageOpt_Model_Override_Mage_Catalog_Product_Image extends Extendware_EWImageOpt_Model_Override_Mage_Catalog_Product_Image_Bridge
{
	static protected $checkFilemtime = false;
	static protected $cacheImages = false;
	
	protected function _construct() {
		self::$checkFilemtime = Mage::helper('ewimageopt/config')->isFilemtimeEnabled();
		self::$cacheImages = Mage::helper('ewimageopt/config')->isCatalogImageCacheEnabled();
		if (Mage::getDesign()->getArea() == 'adminhtml') {
			self::$cacheImages = false;
		}
		self::createHtaccessFile();
		
		return parent::_construct();
	}
	
	static protected function getCacheMediaDir() {
		static $directory = null;
		if ($directory === null) {
			$directory = Mage::getConfig()->getOptions()->getMediaDir() . DS . Mage::helper('ewimageopt/config')->getSlugCustomPath() . DS . 'media' . DS . 'inline';
			if (Mage::getConfig()->getOptions()->createDirIfNotExists($directory) === false) {
	    		Mage::throwException(Mage::helper('catalog')->__('Could not create directory %s', $directory));
	    	}
		}
		
    	return $directory;
	}
	
	public function saveFile()
    {
    	parent::saveFile();
        $filepath = $this->getNewFile();
   		if (Mage::helper('ewimageopt/config')->isCatalogImageOptimizingEnabled() === true) {
   			if (Mage::helper('ewimageopt/config')->getOptimizerMode() == 'realtime') {
   				if (Mage::helper('ewimageopt')->lock('optimizer.lock') === true) {
    				Mage::helper('ewimageopt')->optimizeImage($filepath, false);
    				Mage::helper('ewimageopt')->unlock('optimizer.lock');
   				}
   			}
    	}
        return $this;
    }
    
	public function getUrl()
    {
    	if (self::$cacheImages === false) {
    		return parent::getUrl();
    	}
    	
    	$baseDir = self::getCacheMediaDir();
        $path = str_replace($baseDir . DS, '', $this->_newFile);
        
        return $this->getBaseUrl() . Mage::helper('ewimageopt/config')->getSlugCustomPath() . '/media/inline/' . str_replace(DS, '/', $path);
    }
    
	public function getBaseUrl() {
    	static $baseUrl = null;
    	if ($baseUrl === null) {
    		$baseUrl = Mage::helper('ewimageopt')->rewriteUrl('catalog_image', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA));
    	}
    	
    	return $baseUrl;
    }
    
	public function setBaseFile($file)
    {
        parent::setBaseFile($file);
        
        if (self::$cacheImages === true) {
	        $hash = substr(md5($this->_newFile . '-' . $this->_baseFile), 0, 10);
	        $filename = pathinfo($this->_newFile, PATHINFO_FILENAME);
	        if (Mage::helper('ewimageopt/config')->isCatalogImageSeoEnabled()) {
		        $product = Mage::helper('catalog/image')->getProductForEwimageopt();
		        if ($product and $product->getName()) {
		        	$filename = preg_replace('/\s/', '-', strtolower($product->getName()));
		        	$filename = preg_replace('/[^a-zA-Z0-9_-]+/', '', $filename);
		        	$filename = preg_replace('/-+/', '-', $filename);
		        	$filename = substr($filename, 0, 150);
		        }
	        }
	        $filename .= '-' . substr($hash, 4, 3);
			$this->_newFile = self::getCacheMediaDir() . DS . dechex(ceil(hexdec($hash[0].$hash[1].$hash[2])/16)) . DS . $hash[3] . DS . $filename . '.' . pathinfo($this->_newFile, PATHINFO_EXTENSION);

			if (self::$checkFilemtime === true) {
        		if (@filemtime($this->_baseFile) >= @filemtime($this->_newFile)) {
        			if (@filemtime($this->_newFile) > 0)  @unlink($this->_newFile);
        		}
        	}
        }
        
        return $this;
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
}