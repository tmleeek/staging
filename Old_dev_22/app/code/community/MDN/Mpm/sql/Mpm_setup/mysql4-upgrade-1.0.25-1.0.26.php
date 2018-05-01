<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mpm_stats')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mpm_stats')} (
  id int(11) NOT NULL auto_increment,
  channel varchar(30) NOT NULL,
  segment_type varchar(30),
  segment_value varchar(255),
  competitor varchar(255),
  offers_count int(11),
  bbw_count int(11),
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('mpm_stats')} ADD KEY `channel` (`channel`);
ALTER TABLE {$this->getTable('mpm_stats')} ADD KEY `segment_type` (`segment_type`);
ALTER TABLE {$this->getTable('mpm_stats')} ADD KEY `segment_value` (`segment_value`);

");

$installer->endSetup();

