<?php
$installer = $this;
$installer->startSetup();



$installer->run("
   UPDATE {$this->getTable('core_config_data')}
   SET value = 'Ogone Belfius Direct Net'
   WHERE path = 'payment/ops_belfiusDirectNet/title'
   AND value = 'Ogone BelfiusDirectNet';
 ");

$installer->endSetup();

