<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE  {$this->getTable('mpm_pricing_log')}
ADD behaviour varchar(30);

");

$installer->endSetup();

