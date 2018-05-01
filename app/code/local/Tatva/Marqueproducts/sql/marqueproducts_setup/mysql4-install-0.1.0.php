<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('marqueproducts')};
CREATE TABLE {$this->getTable('marqueproducts')} (
  `marqueproducts_id` int(11) unsigned NOT NULL auto_increment,
  `marque`  varchar(255) NOT NULL default '',
  `collection` text NOT NULL default '',
  `product_ids` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`marqueproducts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 