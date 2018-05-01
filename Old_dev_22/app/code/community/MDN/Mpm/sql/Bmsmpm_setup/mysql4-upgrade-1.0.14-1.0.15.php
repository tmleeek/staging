<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mpm_commission')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mpm_commission')} (
  id int(11) NOT NULL auto_increment,
  channel varchar(30) NOT NULL,
  product_id int NOT NULL,
  percent decimal(6,2),
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('mpm_commission')} ADD KEY `product_id` (`product_id`);
ALTER TABLE {$this->getTable('mpm_commission')} ADD KEY `channel` (`channel`);


");

$installer->endSetup();

