<?php
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('mondialrelay_pointsrelais')};
DROP TABLE IF EXISTS {$this->getTable('mondialrelay_pointsrelaisld1')};
DROP TABLE IF EXISTS {$this->getTable('mondialrelay_pointsrelaislds')};
DELETE FROM {$this->getTable('core/config_data')} WHERE path like 'carriers/pointsrelais/%';
DELETE FROM {$this->getTable('core/resource')} WHERE code like 'pointsrelais_setup';
");

$installer->endSetup();
