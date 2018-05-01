<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE  {$this->getTable('mpm_rules')} ADD is_system TINYINT default 0;

ALTER TABLE {$this->getTable('mpm_rules')} ADD KEY `type` (`type`);

");

$installer->endSetup();

