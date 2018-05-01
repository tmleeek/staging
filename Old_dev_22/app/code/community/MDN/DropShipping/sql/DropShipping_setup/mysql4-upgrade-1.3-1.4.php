<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('purchase_supplier')}` 
ADD `sup_csv_cost_col_num` VARCHAR(10);
");

