<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `freegift`
ADD `store_id` smallint(5) unsigned NOT NULL AFTER `gift_product_id`,
ADD FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ");

$installer->endSetup(); 