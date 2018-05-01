<?php
if (defined('DS') === false) define('DS', DIRECTORY_SEPARATOR);
if (defined('PS') === false) define('PS', PATH_SEPARATOR);
if (defined('BP') === false) define('BP', dirname(dirname(dirname(dirname(dirname(__FILE__))))));
__ewDependencyCheck();
// we always must do this if compilation is enabled due to the double declaration of Zend_Cache_Backend otherwise
if (defined('COMPILER_INCLUDE_PATH') === true or class_exists('Extendware_EWCore_Model_Autoload') === false) {
	if (defined('COMPILER_INCLUDE_PATH')) include_once(COMPILER_INCLUDE_PATH . DS . 'Varien/Autoload.php');
	else include_once(BP . DS . 'lib/Varien/Autoload.php');
	Varien_Autoload::register();
}

// get our custom auto loader
$autoloader = new Extendware_EWCore_Model_Autoload();

// you can set this to false and it will increase performance. you will need to ensure that you
// clear extendware core cache whenever you edit files (edit core files, update extensions, etc).
// $autoloader->setOption('check_mtime', false);

// set this to true if you want Extendware code to be loaded from a mounted ram disk
// you will need to the disk at [magento root]/var/extendware/system/memfs/ using something
// like mount -t tmpfs -o size=20M tmpfs [magento root]/var/extendware/system/memfs/
// doing this can further increase performance of Extendware extensions. if you do not know
// how to mount a tmpfs or understand how it could be beneficial, then you should leave this commented
// $autoloader->setOption('use_memfs', true);

// use this to use [magento root]/var/extendware/system/memfs/ for override files whether.
// this is mostly useful only if memfs directory is a mounted ram disk. this has the potential
// to speed some information such as checking mtime or existence of the file
// $autoloader->setOption('use_memfs_for_overrides', true);

// use this to use [magento root]/var/extendware/system/memfs/ for override files whether.
// this is mostly useful only if memfs directory is a mounted ram disk. this has the potential
// to speed some information such as checking mtime or existence of the file
// $autoloader->setOption('override_path', '/full/path/to/directory');

// if you have turned of file modification checking in APC, then force file evaluation
// this will make it so apc does not cache the file. note: disabling stat checking in apc
// is NOT recommended as it can cause issues in magento under certain circumstances
if ((ini_get('apc.stat') != '' and !ini_get('apc.stat')) or (ini_get('eaccelerator.check_mtime') != '' and !ini_get('eaccelerator.check_mtime'))) {
	$autoloader->setOption('force_php_evaluation', true);
}

// manually add overrides that need to be set before we initalize
// aitoc will delete this autload, so we must override it first and then set their autoload after ours
$autoloader->addOverride('model', 'Aitoc_Aitsys_Model_Rewriter_Autoload', 'Extendware_EWCore_Model_Override_Aitoc_Aitsys_Rewriter_Autoload');

// this is used by our cache optimizer extension and it can be deleted if you do not use this extension
$autoloader->addOverride('model', 'Mage_Core_Model_Cache', 'Extendware_EWCacheBackend_Model_Override_Mage_Core_Cache');

// unregister autoloaders (Varien_Autoloader will be added back later by varien code)
$functions = spl_autoload_functions();
foreach ($functions as $function) {
	spl_autoload_unregister($function);
}

// regster our auto loader as the first autoloader
spl_autoload_register(array($autoloader, 'autoload'));

// this is used by the store / currency auto switching. if you do not need this you can delete it
if (isset($_SERVER['REQUEST_METHOD']) === true and (isset($_COOKIE['frontend']) === false or isset($_GET['__currency']) === true)) {
	if (@is_file($autoloader->getMemoryReadFilePath(BP.DS.'app'.DS.'etc'.DS.'Extendware_EWAutoSwitcher.php')) === true) {
		$autoloader->setOption('can_load_all', true);
		try {
			$helper = new Extendware_EWAutoSwitcher_Helper_Data();
			if ($helper->getConfig()->isEnabled() === true and $helper->isEnabledForUserAgent() === true) {
				$websiteId = $helper->getDeterminedWebsiteId();
				if (!$websiteId) $websiteId = $helper->getDeterminedWebsiteId('hostname');
				$storeCode = $helper->autoSwitchToStore($websiteId);
				if (!$storeCode and $websiteId > 0) {
					$helper->setCookieSettingsFromWebsite($websiteId);
				}
				$currencyCode = $helper->autoSwitchToCurrency($storeCode, $websiteId);
			}
		} catch (Exception $e) {}
		$autoloader->setOption('can_load_all', false);
	}
}

// this is used by the full page cache. if you are not using the full page cache
// then you can delete this and it will not cause any problems.
if (isset($_SERVER['REQUEST_METHOD']) === true) {
	if (
		@is_file($autoloader->getMemoryReadFilePath(BP.DS.'app'.DS.'etc'.DS.'Extendware_EWPageCache.php')) === true or 
		// xml checking is only done for older versions. this can be deleted in newer versions
		@is_file($autoloader->getMemoryReadFilePath(BP.DS.'app'.DS.'etc'.DS.'Extendware_EWPageCache.xml')) === true
	) {
		$autoloader->setOption('can_load_all', true);
		try {
			if (class_exists('Extendware_EWPageCache_Model_Request_Processor_Primary') === true) {
				// error suppression is added because some customers run new extendware core / old page cache
				$content = @Extendware_EWPageCache_Model_Request_Processor_Primary::extractContent();
				if (isset($content{0}) === true) {
					echo $content; 
					exit;
				}
			}
		} catch (Exception $e) {}
		$autoloader->setOption('can_load_all', false);
	}
}

// this is used by cookie message. if you are not using this you can delete this
if (isset($_SERVER['REQUEST_METHOD']) === true and isset($_COOKIE['ewcountry']) === false) {
	if (@is_file($autoloader->getMemoryReadFilePath(BP.DS.'app'.DS.'etc'.DS.'Extendware_EWCookieMessage.php')) === true) {
		$autoloader->setOption('can_load_all', true);
		try {
			$helper = new Extendware_EWCookieMessage_Helper_Data();
			if ($helper->getConfig()->isEnabled() === true) {
				try {
					$helper->sendCountryCookie();
				} catch (Exception $e) {}
			}
		} catch (Exception $e) {}
		$autoloader->setOption('can_load_all', false);
	}
}

// ensure that the normal autoloader has been included
if (class_exists('Varien_Autoload') === false) {
	if (defined('COMPILER_INCLUDE_PATH')) include_once(COMPILER_INCLUDE_PATH . DS . 'Varien/Autoload.php');
	else include_once(BP . DS . 'lib/Varien/Autoload.php');
}

// these are some convenience functions. you can delete __extendwareErrorHandler() if you really want to
// __ewDisableModule sounds scary, but you do not want to modify it
function __extendwareErrorHandler($errno, $errstr, $errfile, $errline) {
	if ($errno == 0) return;
	if (class_exists('Mage', false) === false) return;
	static $maxLogItems = 25; // prevent infinite loops
	if ($maxLogItems-- > 0) {
		$isDeveloperMode = Mage::getIsDeveloperMode();
		Mage::setIsDeveloperMode(true);
		
		try {
			mageCoreErrorHandler($errno, $errstr, $errfile, $errline);
		} catch (Exception $e) {
			if ($isDeveloperMode) {
				throw $e;
			} else {
		        Mage::log($e->getMessage(), Zend_Log::ERR);
		    }
		    
		    try {
    			if (Mage::getConfig()) {
    		        if (Mage::getStoreConfig('ewcore_developer/system_exception_log/enabled')) {
    			        $file = 'exception_system.log';
    			        Mage::log("\n" . $e->__toString(), Zend_Log::ERR, $file);
    		        }
    			}
		    } catch (Exception $e) {}
		}
		
		Mage::setIsDeveloperMode($isDeveloperMode);
    } else {
    	mageCoreErrorHandler($errno, $errstr, $errfile, $errline);
    }
}

function __ewDisableModule($module) {
	if (class_exists('Mage', false) === false) return;
	try {
		if (Mage::helper('ewcore/config')->isViolationDisablingEnabled() === true) {
			Mage::getModel('compiler/process')->registerIncludePath(false);
			$configTools = Extendware::helper('ewcore/config_tools');
			if ($configTools) $configTools->disableModule($module);
		}
	} catch (Exception $e) { Mage::logException($e); }
}

function __ewDependencyCheck() {
	if (function_exists('ioncube_license_properties') === false) {
		$files = array();
		$files[] = BP . DS . 'app' . DS . 'code' . DS . 'local' . DS . 'Extendware' . DS . 'EWCore' . DS . 'de.php';
		$files[] = BP . DS . 'app' . DS . 'code' . DS . 'community' . DS . 'Extendware' . DS . 'EWCore' . DS . 'de.php';
		foreach ($files as $file) {
			if (is_file($file)) {
				include $file;
				exit;
			}
		}
		die('IonCube is required to be installed. Please contact your hosting provider');
	}
}