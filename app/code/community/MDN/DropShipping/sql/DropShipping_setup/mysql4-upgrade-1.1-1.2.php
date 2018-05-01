<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("
ALTER TABLE  `{$this->getTable('sales_flat_order_item')}` 
ADD  `dropship_comments` TEXT;

");
 
$installer->endSetup();


