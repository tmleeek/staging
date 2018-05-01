<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("
    
ALTER TABLE  `{$this->getTable('purchase_supplier')}` ADD  `sup_dropshipping_tpl_order` TEXT DEFAULT  '';
ALTER TABLE  `{$this->getTable('purchase_supplier')}` ADD  `sup_dropshipping_tpl_order_item` TEXT DEFAULT  '';
    
");

