<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mpm_product_offers')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mpm_product_offers')} (
  id int(11) NOT NULL auto_increment,
  product_id int(11) NOT NULL,
  channel varchar(30) NOT NULL,
  seller_id varchar(30),
  seller_name varchar(255),
  price decimal(6,2) NOT NULL,
  shipping decimal(6,2) NOT NULL,
  total decimal(6,2) NOT NULL,
  updated_at datetime NOT NULL,
  rank TINYINT,
  PRIMARY KEY  (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('mpm_product_offers')} ADD KEY `product_id` (`product_id`);
ALTER TABLE {$this->getTable('mpm_product_offers')} ADD KEY `channel` (`channel`);

");

$installer->endSetup();

