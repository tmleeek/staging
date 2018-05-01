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
list ($action, $identifier, $depth) = @array($argv[1], $argv[2], (int)$argv[3]);
if (in_array($depth, array(0, 1)) === false) die('ERROR - depth is incorrect or maximum depth has been met');

if (in_array($action, array('help', '--help', '-h')) ===true) {
	echo "Usage: php license.php [action] [identifier]\n\n";
	echo "Identifier: a string with no spaces (a-z0-9_.-) that is used to identifier the installation that will be creaed.\n\n";
	echo "Supported Actions:\n";
	echo "\t * create - this will create a new installation and create instances. It will error if an installation with the [identifier] already exists. It is recommended to always use this to ensure you do not pay for more licenses than you need. You do NOT need to create a new installation for development-only installations (use the update method instead).\n";
	echo "\t * update - only fetch licenses if the installation identified by [identifier] has previously been created. If installation does not exist then it will error. It is recommended to always use this if you are needing to get licenses for a development store of the identified installation.\n\n";
	echo "\t * createupdate - if installation does not exist it will be created. If it does exist then an update will be performed. Use this with caution as it is very easy to input the wrong [identifier] and license new instances.\n\n";
	exit;
}

if ($depth == 0) {
	// disable system compilation and cache
	Mage::getModel('compiler/process')->registerIncludePath(false);
	__setCacheStatus(0);
	Mage::app()->cleanCache();
	
	$o = $r = null;
	$cmd = sprintf('php %s %s %s %s', escapeshellarg(__FILE__), escapeshellarg($action), escapeshellarg($identifier), escapeshellarg($depth+1));
	@exec($cmd, $o, $r);
	$o = implode("\n", $o);
	if (strpos($o, 'OK') !== false) {
		__setCacheStatus(1);
		Mage::app()->cleanCache();
	}
	die($o);
}

if (!@class_exists('Extendware_EWCore_Model_Autoload')) {
	die('ERROR - Extendware Core does not appear to be installed');
}

if (in_array($action, array('create', 'createupdate', 'update')) === false) {
	die('ERROR - unknown action');
}

if (!$identifier or !preg_match('/^[a-zA-Z0-9_.-]+$/', $identifier)) {
	die('ERROR - identifier is not correct');
}

$files = @glob(BP.DS.'var' . DS . 'extendware'.DS.'system'.DS.'licenses'.DS.'*.*');
$files = @array_merge($files, glob(BP.DS.'var' . DS . 'extendware'.DS.'system'.DS.'serials'.DS.'*.*'));
$files = @array_merge($files, glob(BP.DS.'var' . DS . 'extendware'.DS.'system'.DS.'licenses'.DS.'encoder'.DS.'*.*'));
$files = @array_merge($files, glob(BP.DS.'app' . DS . 'etc'.DS.'modules'.DS.'*.*'));
foreach ($files as $file) {
	if (is_writeable($file) === false) {
		die(sprintf('ERROR - File is not writeable %s', $file));
	}
}       

$config = @simplexml_load_file(dirname(__FILE__) . DS . 'config.xml');
if (!$config) $config = @simplexml_load_string('<config></config>');
$modules = array();
$collection = Mage::getSingleton('ewcore/module')->getCollection();
foreach ($collection as $module) {
	if ($module->isExtendware() === false) continue;
	if ($module->isForMainSite() === true) continue;
	$modules[$module->getIdentifier()] = array(
		'addons' => '#all',
		'plan' => 'default',
		'instance_id' => $module->getSerial()->getInstanceId(),
		'installation_id' => $module->getSerial()->getInstallationId(),
		'release_version' => $module->getSerial()->getRelease()->getVersion(),
		'release_variant_id' => $module->getSerial()->getRelease()->getVariantId(),
	);
	
	if (isset($config->modules->{$module->getIdentifier()}->addons)) {
		$modules[$module->getIdentifier()]['addons'] = (string)$config->modules->{$module->getIdentifier()}->addons;
	}

	if (isset($config->modules->{$module->getIdentifier()}->plan)) {
		$modules[$module->getIdentifier()]['plan'] = (string)$config->modules->{$module->getIdentifier()}->plan;
	}
}

$result = true;
$affectedModules = array();
try {
	$licenser = Mage::getModel('ewcore/module_installer_licenser');
	$result = $licenser->autoLicenseInstallation($action, $identifier, $modules);
	if ($result === false) Mage::throwException($licenser->getLastApiErrorMessage());
	$affectedModules = $licenser->getAffectedModules();
} catch (Exception $e) {
	$result = false;
	Mage::logException($e);
	die('ERROR - ' . $e->getMessage());
}

if ($result === false) {
	die('ERROR');
}

foreach ($affectedModules as $identifier) {
	$script = dirname(__FILE__) . DS . 'enable_module.php';
	$cmd = sprintf('php %s %s > /dev/null 2>&1', escapeshellarg($script), escapeshellarg($identifier));
	@exec($cmd, $o, $r);
}

die('OK');


function __setCacheStatus($status) {
	$allTypes = Mage::app()->useCache();
	foreach ($allTypes as $code => $oldStatus) {
		$allTypes[$code] = $status;
	}
	Mage::app()->saveUseCache($allTypes);
}
