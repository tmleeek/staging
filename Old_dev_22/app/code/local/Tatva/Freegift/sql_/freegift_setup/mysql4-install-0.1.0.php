<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('freegift')};
CREATE TABLE {$this->getTable('freegift')} (
  `freegift_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `entity_id` int(10) unsigned NOT NULL,
  `gift_product_id` int(10) unsigned NOT NULL,
  FOREIGN KEY (`entity_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`gift_product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 