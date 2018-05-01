<?php
$paths = array(
    dirname(dirname(dirname(dirname(__FILE__)))) . '/app/Mage.php',
	'../../../../../app/Mage.php',
	'../../../../app/Mage.php',
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
if (class_exists('Extendware') === false) die('ERROR - extendware not found');

if (in_array($argv[1], array('help', '--help', '-h')) ===true) {
	echo "Usage: php enable_module.php [identifier]\n\n";
	echo "Identifier: the technial name of the extension. Example: Extendware_EWCore, Extendware_EWPageCache, etc\n\n";
	exit;
}

$moduleIdentifier = $argv[1];
$module = Mage::getSingleton('ewcore/module')->load($moduleIdentifier);
if (!$module->getId()) die('ERROR - could not find module with this identifier');

try {
	Mage::getModel('compiler/process')->registerIncludePath(false);
	Mage::helper('ewcore/config_tools')->enableModule($module->getIdentifier());
} catch (Exception $e) {
	Mage::logException($e);
	die('ERROR - ' . $e->getMessage());
}

die('OK');
