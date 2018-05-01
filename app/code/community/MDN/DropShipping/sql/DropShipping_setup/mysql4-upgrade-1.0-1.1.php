<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("


ALTER TABLE  `{$this->getTable('sales_flat_order_item')}` 
ADD  `dropship_status` varchar(50);

ALTER TABLE  `{$this->getTable('sales_flat_order_item')}` 
ADD  `dropship_supplier_id` INT;

");
 
$installer->endSetup();


