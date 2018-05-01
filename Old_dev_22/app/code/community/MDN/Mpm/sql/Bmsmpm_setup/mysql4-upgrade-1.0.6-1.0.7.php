<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('mpm_pricing_log')} ADD debug varchar(255);

");

$installer->endSetup();

