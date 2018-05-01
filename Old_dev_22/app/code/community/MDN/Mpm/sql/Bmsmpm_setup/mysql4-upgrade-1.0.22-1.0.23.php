<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE  {$this->getTable('mpm_commission')} CHANGE  `percent`  `percent` DECIMAL( 5, 1 ) NULL DEFAULT NULL;

ALTER TABLE  {$this->getTable('mpm_pricing_log')} CHANGE  `margin`  `margin` DECIMAL( 3, 1 ) NULL DEFAULT NULL;
ALTER TABLE  {$this->getTable('mpm_pricing_log')} CHANGE  `margin_for_bbw`  `margin_for_bbw` DECIMAL( 3, 1 ) NULL DEFAULT NULL;

");

$installer->endSetup();

