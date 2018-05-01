<?php

$installer = $this;
$installer->startSetup();

$installer->run("
  CREATE TABLE IF NOT EXISTS {$this->getTable('phpro_translate')} (
  `translate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `string` varchar(255) NOT NULL DEFAULT '',
  `module` varchar(255) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `locale` varchar(20) NOT NULL DEFAULT '',
  `interface` varchar(20) NOT NULL,
  PRIMARY KEY (`translate_id`),
  UNIQUE KEY `string` (`string`,`module`,`store_id`,`locale`,`interface`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$installer->endSetup();