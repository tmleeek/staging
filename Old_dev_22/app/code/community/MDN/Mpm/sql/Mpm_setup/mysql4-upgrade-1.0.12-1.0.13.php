<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE  {$this->getTable('mpm_reports')}
ADD report_type varchar(30);

");

$installer->endSetup();

