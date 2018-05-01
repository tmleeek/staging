<?php
$installer = $this;
$installer->startSetup();
$installer->run("Alter TABLE {$this->getTable('gls_unibox_shipment')} modify `weight` FLOAT;");
$installer->endSetup();