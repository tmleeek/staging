<?php


$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("


ALTER TABLE {$this->getTable('mpm_product_offers')} ADD KEY `seller_name` (`seller_name`);
ALTER TABLE {$this->getTable('mpm_product_offers')} ADD KEY `rank` (`rank`);

");

$installer->endSetup();

