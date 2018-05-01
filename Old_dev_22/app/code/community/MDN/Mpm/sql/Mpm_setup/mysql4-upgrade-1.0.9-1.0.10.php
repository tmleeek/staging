<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

ALTER TABLE  {$this->getTable('mpm_pricing_log')}
ADD status varchar(30);
ALTER TABLE  {$this->getTable('mpm_pricing_log')}
ADD is_current tinyint default 0;

ALTER TABLE {$this->getTable('mpm_pricing_log')} ADD KEY `status` (`status`);
ALTER TABLE {$this->getTable('mpm_pricing_log')} ADD KEY `is_current` (`is_current`);

");

$installer->endSetup();

