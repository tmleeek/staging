<?php
list($script, $basePath, $file) = $argv;

require $basePath .  '/app/Mage.php';
Mage::app('admin')->setUseSessionInUrl(false);

$verboseLogging = Mage::helper('ewimageopt/config')->isVerboseLogEnabled();

$copiedFile = dirname($file) . DS . '__' . mt_rand(1, 9999) . basename($file);
if (@copy($file, $copiedFile) === false) {
	if ($verboseLogging) Mage::helper('ewimageopt/system')->log(Mage::helper('ewimageopt/system')->__('Could not copy %s to %s', $file, $copiedFile));
	exit;
}

Mage::helper('ewimageopt')->optimizeImage($copiedFile, false);

if (filesize($copiedFile) and filesize($copiedFile) < filesize($file)) {
	if (@rename($copiedFile, $file) === false) {
		@file_put_contents($file, file_get_contents($copiedFile));
	}
}

@unlink($copiedFile);