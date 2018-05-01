<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('sales_flat_order')} ADD `relay_name` VARCHAR(255) NOT NULL AFTER `gift_message_id` ,
ADD `relay_address` VARCHAR(255) NOT NULL AFTER `relay_name` ,
ADD `relay_address2` VARCHAR(255) NOT NULL AFTER `relay_address` ,
ADD `relay_address3` VARCHAR(255) NOT NULL AFTER `relay_address2` ,
ADD `relay_city` VARCHAR(255) NOT NULL AFTER `relay_address3` ,
ADD `relay_postalcode` VARCHAR(255) NOT NULL AFTER `relay_city` ,
ADD `relay_id` INT(11) NOT NULL AFTER `relay_postalcode` ,
ADD `relay_code` VARCHAR(255) NOT NULL AFTER `relay_id`
");

$installer->endSetup();

?>