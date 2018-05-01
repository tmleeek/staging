#!/usr/bin/php -q
<?php
if (isset($_SERVER['REQUEST_METHOD'])) exit;
if (defined('DS') === false) define('DS', DIRECTORY_SEPARATOR);
if (defined('PS') === false) define('PS', PATH_SEPARATOR);
if (defined('BP') === false) define('BP', dirname(dirname(dirname(dirname(dirname(__FILE__))))));
if (defined('CURRENT_FILE') === false) define('CURRENT_FILE', __FILE__);

ini_set('display_errors', 0);
ini_set('output_buffering', 'off');
while (@ob_end_flush());
ini_set('implicit_flush', true);
ob_implicit_flush(true);

$updateConfigFunc = function (&$isEnabled, &$expiration, &$segmentableUseragents, &$segmentableCookies, &$isParameterSortingEnabled, &$ignoredParameters, &$isVirtualKeysEnabled) {
	@clearstatcache();
	$config = new Extendware_EWPageCache_Helper_Config();
	if ($config->hasFallbackStorage() === true) {
		$isEnabled = $config->isLighteningCacheEnabled();
		$expiration = $config->getCacheLifetime();
		$isVirtualKeysEnabled = $config->isVirtualKeysEnabled();
		
		$segmentableUseragents = $config->getSegmentableUserAgents();
		$segmentableCookies = array();
		foreach ($config->getSegmentableCookies() as $key => $value) {
			$segmentableCookies[$key] = $key;
		}
		
		$isParameterSortingEnabled = $config->isParameterSortingEnabled();
		$ignoredParameters = $config->getIgnoredParameters();
		$ignoredParameters = array_combine($ignoredParameters, $ignoredParameters);
		unset($config);
	} else {
		$isEnabled = false;
	}
	return true;
};

$extractCookiesFunc = function($string) {
	if (isset($string{0}) === false) return array();
	$cookies = array();
	$pairs = explode(' ', $string);
	foreach ($pairs as $pair) {
		list($name, $value) = explode('=', rtrim($pair, ';'), 2);
		$cookies[$name] = rawurldecode($value);
	}
	
	return $cookies;
};

$files = array(BP . '/app/code/local/Extendware/EWCore/Model/Autoload.php', BP . '/app/code/community/Extendware/EWCore/Model/Autoload.php');
foreach ($files as $file) {
	if (is_file($file)) {
		include_once $file;
		break;
	}
}
if (class_exists('Extendware_EWCore_Model_Autoload', false) === false) exit;

$autoloader = new Extendware_EWCore_Model_Autoload();
spl_autoload_register(array($autoloader, 'autoload'));
if ((ini_get('apc.stat') != '' and !ini_get('apc.stat')) or (ini_get('eaccelerator.check_mtime') != '' and !ini_get('eaccelerator.check_mtime'))) {
	$autoloader->setOption('force_php_evaluation', true);
}

$autoloader->setOption('can_load_all', true);

$config = new Extendware_EWPageCache_Helper_Config();
$configFile = $config->getFallbackStoragePath();
$lastWroteConfig = @filemtime($configFile);

$isEnabled = $expiration = $segmentableUseragents = $segmentableCookies = $isParameterSortingEnabled = $ignoredParameters = $isVirtualKeysEnabled = null;
$updateConfigFunc($isEnabled, $expiration, $segmentableUseragents, $segmentableCookies, $isParameterSortingEnabled, $ignoredParameters, $isVirtualKeysEnabled);

$toEval = null;
$selfModifiedTime = filemtime(CURRENT_FILE);

$lastClearStatCache = time();
$handle = fopen ('php://stdin','r');
while ($line = fgets($handle)) {
	$line = trim($line);
	if (!$line) {
		echo "\n";
		continue;
	}
	
	if ((time() - $lastClearStatCache) >= 30) {
		$lastClearStatCache = time();
		@clearstatcache();
	}

	if ($selfModifiedTime != @filemtime(CURRENT_FILE)) {
		echo "\n";
		$toEval = @file_get_contents(CURRENT_FILE);
		break;
	}
	
	if ($lastWroteConfig != @filemtime($configFile)) {
		$updateConfigFunc($isEnabled, $expiration, $segmentableUseragents, $segmentableCookies, $isParameterSortingEnabled, $ignoredParameters, $isVirtualKeysEnabled);
		$lastWroteConfig = @filemtime($configFile);
	}
	if (!$isEnabled) { echo "\n"; continue; }
	
	@list($secure, $host, $uri, $query, $cookieString, $filename, $ipAddress, $useragent) = explode(';~;', $line, 8);
	$fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
	if ($fileExtension and in_array($fileExtension, array('html', 'htm', 'php')) === false) { echo "\n"; continue; }
	
	$secure = ($secure == 'on' ? 1 : 0);
	
	parse_str($query, $parameters);
	// ensure crawler bypasses the lightening cache
	if (isset($parameters['__efpcopts']) === true) { echo "\n"; continue; } 
	if (isset($parameters['__no_lightening_cache']) === true) { echo "\n"; continue; } 
	$parameters = array_diff_key($parameters, $ignoredParameters);
	if ($isParameterSortingEnabled) ksort($parameters);
	
	$cookies = array();
	if (isset($cookieString{0})) {
		$cookies = $extractCookiesFunc($cookieString);
		if ($isVirtualKeysEnabled === true and isset($cookies['ewpcvc-t'])) { echo "\n"; continue; } 
		// if in secondary cache then bypass lightening cache
		if (isset($cookies['epc-no-primary-cache']) or isset($_COOKIE['epc-primary-disabler']) or isset($_COOKIE['epc-no-cache']) or isset($_COOKIE['persistent_shopping_cart'])) { echo "\n"; continue; } 
		$cookies = array_intersect_key($cookies, $segmentableCookies);
		ksort($cookies); 
	}
	
	$useragentKey = '';
	foreach ($segmentableUseragents as $group => $regExps) {
		foreach ($regExps as $regExp) {
			if (strpos($regExp, '/') !== 0) $regExp = '/' . trim($regExp, '/') . '/';
			if (@preg_match($regExp, $useragent)) {
				$useragentKey = md5($group);
				break;
			}
		}
	}
	
	$virtualKeys = array(null);
	if ($isVirtualKeysEnabled === true) {
		$virtualKeys = array('default', null);
	}
	
	$didOutput = false;
	foreach ($virtualKeys as $virtualKey) {
		$prehash = $config->getCacheKey($secure, $host, $uri, $parameters, $cookies, $useragentKey, $virtualKey);
		$hash = $config->toLighteningCacheKey($prehash);
		
		$file = BP . sprintf("/var/cache/extendware/ewpagecache/static/%s.html", substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . $hash);
		if ($expiration !== null and @file_exists($file) === true) {
			// clear stat cache every 60 seconds to ensure file operations are correct
			if ((time() - @filemtime($file)) > $expiration) {
				@unlink($file);
			}
		}
		
		if (@file_exists($file) === true) {
			echo $file . "\n";
			$didOutput = true;
			break;
		}
		//$string = sprintf('%s|%s|%s|%s|%s|%s|%s', $secure, $host, $uri, serialize($parameters), serialize($cookies), $useragentKey, $virtualKey);
		//file_put_contents(dirname(CURRENT_FILE) . '/log.txt', $string . ' - ' . $prehash . "\n\n", FILE_APPEND);
	}
	
	if ($didOutput === false) {
		echo "\n";
	}
}
@fclose($handle);
if ($toEval) {
	$toEval = preg_replace('/^.*?<\?php/s', '', $toEval);
	eval($toEval);
}