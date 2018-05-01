<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('mpm_rules_products')} ADD  channel varchar(30) NOT NULL;

");

$installer->endSetup();

