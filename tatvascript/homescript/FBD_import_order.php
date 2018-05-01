<?php

chdir(dirname(__FILE__));
require '../../app/Mage.php';
Mage::app();

//echo "daadada";
Mage::getModel('tatvamarketplaces/fbd')->execute();//exit;

Mage::getModel('tatvamarketplaces/status_fbdinvoice')->execute();//exit;
Mage::getModel('tatvamarketplaces/status_fbdshipment')->execute();//exit;
Mage::getModel('tatvamarketplaces/status_fbdrefund')->execute();exit;

?>