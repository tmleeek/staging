<?php

$installer = $this;
$installer->startSetup();

$paths = array(
	'ewpagecache_tagging/autoflushing/product_event'
);

$content = '';
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->addFieldToFilter('path', $paths);
foreach ($configCollection as $item) {
	$content .= "#####################################################################################\n";
	$content .= '# ' . $item->getScope() . ':' . (int)$item->getScopeId() . ':' . $item->getPath() . "\n";
	$content .= "#####################################################################################\n";
	$content .= $item->getValue() . "\n\n";
	
	$item->delete();
}

$upgradeFile = Mage::helper('ewpagecache/internal_api')->getTmpDir('upgrade') . DS . time() . '-upgrade.txt';
@file_put_contents($upgradeFile, $content);

$installer->endSetup();