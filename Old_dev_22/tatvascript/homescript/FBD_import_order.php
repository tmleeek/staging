<?php
/*define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/app/Mage.php';
Mage::app();*/
chdir(dirname(__FILE__));
require '../../app/Mage.php';
Mage::app();

Mage::getModel('tatvamarketplaces/fbd')->execute();exit;

//Mage::getModel('tatvamarketplaces/status_fbdinvoice')->execute();exit;
//Mage::getModel('tatvamarketplaces/status_fbdshipment')->execute();exit;
//Mage::getModel('tatvamarketplaces/status_fbdrefund')->execute();exit;

?>