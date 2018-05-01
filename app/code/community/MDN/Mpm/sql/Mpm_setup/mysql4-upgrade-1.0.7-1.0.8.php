<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mpm_product_settings')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mpm_product_settings')} (
  id int(11) NOT NULL auto_increment,
  product_id INT,
  channel varchar(20),
  use_config_behaviour TINYINT DEFAULT 1,
  behaviour varchar(50),
  use_config_price TINYINT DEFAULT 1,
  price decimal(10, 2) NULL,
  use_config_rule TINYINT DEFAULT 1,
  rule INT NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('mpm_product_settings')} ADD KEY `product_id` (`product_id`);
ALTER TABLE {$this->getTable('mpm_product_settings')} ADD KEY `channel` (`channel`);

");

$installer->endSetup();

