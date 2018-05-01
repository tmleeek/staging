<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('productproblem')};
CREATE TABLE {$this->getTable('productproblem')} (
  `productproblem_id` int(11) unsigned NOT NULL auto_increment,
  `productid` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `comment` text NOT NULL default '',
  `reply` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`productproblem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `productproblem` ADD INDEX(`productid`);
ALTER TABLE `productproblem` ADD FOREIGN KEY (`productid`) REFERENCES `catalog_product_entity`(`entity_id`) ON DELETE CASCADE;


    ");

$installer->endSetup();