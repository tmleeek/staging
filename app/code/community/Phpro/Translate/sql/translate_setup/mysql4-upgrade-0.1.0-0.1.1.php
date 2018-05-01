<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('phpro_translate')} 
    ADD COLUMN `page` VARCHAR(255) NOT NULL  AFTER `interface` , 
    ADD COLUMN `time` DATETIME NOT NULL  AFTER `page` ;
");

$installer->endSetup();