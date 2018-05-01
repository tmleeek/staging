<?php

require_once '../../app/Mage.php';
Mage::app();
//ini_set ( 'memory_limit', '2048M' );
Mage::getModel('exportaccountancy/exportdata')->exportaccountancy();
Mage::getModel('exportaccountancy/exportcreditmemo')->exportcreditmemo();
echo "done"; exit; ?>