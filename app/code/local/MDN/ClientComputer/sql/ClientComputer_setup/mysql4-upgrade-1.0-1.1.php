<?php
 
$installer = $this;
 
$installer->startSetup();
 
$installer->run("
 
ALTER TABLE  {$this->getTable('client_computer_operation')} ADD  `cco_result` TEXT NOT NULL ;

    ");
 
$installer->endSetup();
