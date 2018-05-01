<?php


$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mpm_queue')};
CREATE TABLE IF NOT EXISTS {$this->getTable('mpm_queue')} (
  id int(11) UNSIGNED NOT NULL auto_increment,
  product_id varchar(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (id)
);

");

$installer->endSetup();

