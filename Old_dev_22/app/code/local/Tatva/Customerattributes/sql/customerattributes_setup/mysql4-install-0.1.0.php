<?php

$installer = $this;

$installer->startSetup();

$tablequote = $this->getTable('sales/quote_address');
$installer->run("ALTER TABLE  $tablequote ADD  `test_attribute` varchar(255) NOT NULL");

$tablequote = $this->getTable('sales/order_address');
$installer->run("ALTER TABLE  $tablequote ADD  `test_attribute` varchar(255) NOT NULL");

$installer->endSetup(); 