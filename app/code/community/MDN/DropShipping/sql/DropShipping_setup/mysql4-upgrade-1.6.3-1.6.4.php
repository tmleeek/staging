<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("
    
ALTER TABLE  `{$this->getTable('purchase_order')}` ADD  `is_drop_ship` TINYINT DEFAULT  0;
    
");

