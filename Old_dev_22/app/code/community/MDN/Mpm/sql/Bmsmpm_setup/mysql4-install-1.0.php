<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mpm_rules')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mpm_rules')} (
  id int(11) NOT NULL auto_increment,
  type varchar(20) NOT NULL,
  name varchar(255) NOT NULL,
  priority tinyint NOT NULL,
  perimeter tinytext NOT NULL,
  content tinytext NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

");

$installer->endSetup();

