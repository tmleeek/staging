<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mpm_pricing_log')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mpm_pricing_log')} (
  id int(11) NOT NULL auto_increment,
  created_at datetime NOT NULL,
  product_id INT,
  channel varchar(20),
  rule_id INT,
  formula varchar(50),
  final_price decimal(6,2),
  error TINYINT,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('mpm_pricing_log')} ADD KEY `product_id` (`product_id`);
ALTER TABLE {$this->getTable('mpm_pricing_log')} ADD KEY `channel` (`channel`);

");

$installer->endSetup();

