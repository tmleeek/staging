<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('mpm_product_offers')} ADD is_me tinyint NOT NULL DEFAULT 0;

");

$installer->endSetup();

