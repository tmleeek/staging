<?php
/*
 * This cronjob will only execute jobs manually added in Extendware -> Manage Extensions -> Crawler -> Jobs and clicking Add Jobs
 */
$paths = array(
    dirname(dirname(dirname(dirname(__FILE__)))) . '/app/Mage.php',
    '../../../app/Mage.php',
    '../../app/Mage.php',
    '../app/Mage.php',
    'app/Mage.php',
);

foreach ($paths as $path) {
    if (file_exists($path)) {
        require $path; 
        break;
    }
}

Mage::app('admin')->setUseSessionInUrl(false);
error_reporting(E_ALL | E_STRICT);
if (file_exists(BP.DS.'maintenance.flag')) exit;
if (class_exists('Extendware') === false) exit;
if (Extendware::helper('ewcrawler') === false) exit;
if (!isset($argv) or !is_array($argv)) $argv = array();

try {
	@set_time_limit(0);
	@ini_set('memory_limit','4096M');
	$crawler = Mage::getModel('ewcrawler/crawler');
	if ($crawler) {
		$options = array('verbose' => in_array('-v', $argv), 'manual_only' => true);
	    $crawler->crawl($options);
	}
} catch (Exception $e) {
	Mage::logException($e);
}