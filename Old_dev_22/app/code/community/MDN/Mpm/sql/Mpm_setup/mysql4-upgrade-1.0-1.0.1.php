<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mpm_rules_products')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mpm_rules_products')} (
  id int(11) NOT NULL auto_increment,
  rule_id int(11) NOT NULL,
  product_id int(11) NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('mpm_rules_products')} ADD KEY `rule_id` (`rule_id`);
ALTER TABLE {$this->getTable('mpm_rules_products')} ADD KEY `product_id` (`product_id`);

");

$installer->endSetup();

