<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mpm_reports')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mpm_reports')} (
  id int(11) NOT NULL auto_increment,
  report_id int(11) NOT NULL,
  requested_at datetime NOT NULL,
  status varchar(15),
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$installer->endSetup();

