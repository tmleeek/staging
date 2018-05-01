<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('purchase_supplier')}` 
ADD `sup_dropshipping_export_type` VARCHAR(10);
");

