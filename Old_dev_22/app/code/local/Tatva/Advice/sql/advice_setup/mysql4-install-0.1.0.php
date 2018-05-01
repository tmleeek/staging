<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('advice')};
CREATE TABLE {$this->getTable('advice')} (
  `advice_id` int(11) unsigned NOT NULL auto_increment,
  `advice_fr` text NOT NULL default '',
  `advice_en` text NOT NULL default '',
  `material` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`advice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 