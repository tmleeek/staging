<?php
define('MAGENTO', realpath(dirname(__FILE__)));
require_once '../app/Mage.php';
Mage::app();
//ini_set ( 'memory_limit', '2048M' );    
Mage::getModel('cibleweb/paarlyxml')->runSaveCatalog();
echo "done"; exit; ?>