<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("
    
ALTER TABLE  `{$this->getTable('purchase_supplier')}` ADD  `sup_dropshipping_match_mode` varchar(20) DEFAULT  'sku';
    
");

