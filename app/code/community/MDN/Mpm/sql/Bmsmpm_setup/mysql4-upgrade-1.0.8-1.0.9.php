<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE  {$this->getTable('mpm_rules')}
CHANGE `perimeter`  `perimeter` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
CHANGE  `content`  `content` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

");

$installer->endSetup();

