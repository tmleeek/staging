<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('tatva_shipping_marketmethod')};
CREATE TABLE {$this->getTable('tatva_shipping_marketmethod')} (
       shipping_marketmethod_id INT(10) NOT NULL AUTO_INCREMENT
     , shipping_code_amazon varchar(255) unsigned NOT NULL
     , shipping_code_ebay varchar(255) unsigned NOT NULL
     , market_weight_from 	decimal(12,4) unsigned NOT NULL
     , market_weight_to 	decimal(12,4) unsigned NOT NULL
     , market_ordertotal_from 	decimal(12,4) unsigned NOT NULL
     , market_ordertotal_to 	decimal(12,4) unsigned NOT NULL
     , market_shipping_code varchar(255) unsigned NOT NULL
     , countries_ids text unsigned NOT NULL
     , PRIMARY KEY (shipping_marketmethod_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();