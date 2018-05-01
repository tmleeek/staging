<?php

$installer = $this;

$installer->startSetup();
$installer->run("
    CREATE TABLE IF NOT EXISTS {$this->getTable('auto_cancel_order_log')}
    (
      `aco_id` int(11) NOT NULL AUTO_INCREMENT,
      `aco_date` datetime NOT NULL,
      `aco_message` varchar(250) NOT NULL,
      PRIMARY KEY (`aco_id`)
    )  ENGINE=InnoDB DEFAULT CHARSET=utf8
");
$installer->endSetup();
