<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("
    
ALTER TABLE  `{$this->getTable('purchase_supplier')}`
CHANGE  `sup_dropshipping_export_type`  `sup_dropshipping_export_type` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT  'pdf';

");

