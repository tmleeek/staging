<?php
ini_set('display_errors', 0);
if (!isset($_SERVER['REQUEST_METHOD'])) return;
if (defined('DS') === false) define('DS', DIRECTORY_SEPARATOR);
if (defined('PS') === false) define('PS', PATH_SEPARATOR);
if (defined('BP') === false) define('BP', dirname(dirname(dirname(dirname(dirname(__FILE__))))));

$files = array(BP . '/app/code/local/Extendware/EWCore/Model/Autoload.php', BP . '/app/code/community/Extendware/EWCore/Model/Autoload.php');
foreach ($files as $file) {
	if (is_file($file)) {
		include_once $file;
		break;
	}
}

if (class_exists('Extendware_EWCore_Model_Autoload', false) === false) return;
if (file_exists(BP.DS.'app'.DS.'etc'.DS.'Extendware_EWPageCache.php') === false) return;
if (@($_SERVER['REQUEST_METHOD'] == 'POST')) return;

$autoloader = @new Extendware_EWCore_Model_Autoload();
spl_autoload_register(array($autoloader, 'autoload'));
if ((ini_get('apc.stat') != '' and !ini_get('apc.stat')) or (ini_get('eaccelerator.check_mtime') != '' and !ini_get('eaccelerator.check_mtime'))) {
	$autoloader->setOption('force_php_evaluation', true);
}

$autoloader->setOption('can_load_all', true);

__ewpcLoad();
function __ewpcLoad() {
	$config = new Extendware_EWPageCache_Helper_Config();
	if ($config->isLighteningCacheEnabled() === false) return;
	$microTime = microtime(true);
	
	$expiration = $config->getCacheLifetime();
	$segmentableCookies = array('ewpcvc-t' => 'ewpcvc-t');
	foreach ($config->getSegmentableCookies() as $key => $value) {
		$segmentableCookies[$key] = $key;
	}
	
	$isVirtualKeysEnabled = $config->isVirtualKeysEnabled();
	$segmentableUseragents = $config->getSegmentableUserAgents();
	$isParameterSortingEnabled = $config->isParameterSortingEnabled();
	$ignoredParameters = $config->getIgnoredParameters();
	$ignoredParameters = array_combine($ignoredParameters, $ignoredParameters);
	
	$secure = ((isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on') ? 1 : 0);
	
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$host = $_SERVER['HTTP_HOST'];
	$uri = $_SERVER['REQUEST_URI'];
	if (strpos($uri, '?') !== false) {
		$uri = substr($uri, 0, strpos($uri, '?'));
	}
	
	$parameters = $_GET;
	// ensure crawler bypasses the lightening cache
	if (isset($parameters['__efpcopts']) === true) { return; }
	if (isset($parameters['__no_lightening_cache']) === true) { return; } 
	$parameters = array_diff_key($parameters, $ignoredParameters);
	if ($isParameterSortingEnabled) ksort($parameters);
	
	$cookies = $_COOKIE;
	if ($isVirtualKeysEnabled === true and isset($cookies['ewpcvc-t'])) { return; } 
	// if in secondary cache then bypass lightening cache
	if (isset($cookies['epc-no-primary-cache']) or isset($_COOKIE['epc-primary-disabler']) or isset($_COOKIE['epc-no-cache']) or isset($_COOKIE['persistent_shopping_cart'])) { return; } 
	$cookies = array_intersect_key($cookies, $segmentableCookies);
	ksort($cookies); 
		
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
	
	foreach ($virtualKeys as $virtualKey) {
		$hash = $config->getCacheKey($secure, $host, $uri, $parameters, $cookies, $useragentKey, $virtualKey);
		
		$file = BP . sprintf("/var/cache/extendware/ewpagecache/static/%s.html", substr($hash, 0, 2) . '/' . substr($hash, 2, 2) . '/' . $hash);
		if (@file_exists($file) === false) continue;
		if ($expiration !== null) {
			if ((time() - @filemtime($file)) > $expiration) {
				@unlink($file);
				continue;
			}
		}
	
		$page = gzuncompress(file_get_contents($file));
		if ($config->isOutputHeadersEnabled() === true or $config->isFooterWidgetEnabled() === true) {
			$helper = new Extendware_EWPageCache_Helper_Data();
			if (@$helper->isAllowedByIpRules() === true) {
				if ($config->isOutputHeadersEnabled() === true) {
					header('X-PageCache-Ttl: ' . ($config->getCacheLifetime() - @filemtime($file)));
					header('X-PageCache-Defaultable: ' . 1);
					header('X-PageCache-Level: ' . 0);
					header('X-PageCache-ParseTime: ' . (microtime(true) - $microTime));
					header('X-PageCache-Key: ' . $hash);
				}
				
				if ($config->isFooterWidgetEnabled() === true) {
					$page = $helper->injectFooterWidget($page, 'lightening', 1, null, $hash, ($config->getCacheLifetime() - @filemtime($file)));
				}
			}
		}
		
		if ($config->isWidgetEnabled() === true) {
			$timeDiff = (microtime(true) - $microTime);
			$page = $helper->injectWidget($page, 'primary', $timeDiff);
		}
		echo $page;
		exit;
	}
}