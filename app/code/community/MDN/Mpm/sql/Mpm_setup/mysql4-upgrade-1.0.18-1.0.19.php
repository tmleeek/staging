<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE  {$this->getTable('mpm_pricing_log')} ADD bbw_name varchar(50);
ALTER TABLE  {$this->getTable('mpm_pricing_log')} ADD bbw_price decimal(6, 2);
ALTER TABLE  {$this->getTable('mpm_pricing_log')} ADD my_rank tinyint;
ALTER TABLE  {$this->getTable('mpm_pricing_log')} ADD margin tinyint;
ALTER TABLE  {$this->getTable('mpm_pricing_log')} ADD margin_for_bbw tinyint;

");

$installer->endSetup();

