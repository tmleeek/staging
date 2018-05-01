<?php
/*define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/app/Mage.php';
Mage::app();*/
chdir(dirname(__FILE__));
require '../../app/Mage.php';
Mage::app();

Mage::getModel('magebackup/schedule')->mediabackup();exit;





?>
