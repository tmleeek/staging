<?php

$installer = $this;

$installer->startSetup();

$installer->run("


-- DROP TABLE IF EXISTS {$this->getTable('collectionpages')};
CREATE TABLE IF NOT EXISTS {$this->getTable('collectionpages')} (
  `collectionpages_id` int(11) unsigned NOT NULL auto_increment,
  `option_value` varchar(255) NULL default '',
  `option_id` int(11) unsigned NULL default '0',
  `status` varchar(255) NULL default '1',
  PRIMARY KEY (`collectionpages_id` , `option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");



$installer->endSetup();