<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("
    
ALTER TABLE  `{$this->getTable('purchase_supplier')}` ADD  `sup_dropshipping_default_shipping_fees` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `{$this->getTable('purchase_supplier')}` ADD  `sup_dropshipping_mulitply_shipping_fees` TINYINT NOT NULL DEFAULT  '0';
    
");

