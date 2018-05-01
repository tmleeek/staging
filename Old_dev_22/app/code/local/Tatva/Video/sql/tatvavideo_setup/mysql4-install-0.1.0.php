<?php

$installer = $this;

$installer->startSetup();


// EXIG FOU-001 FOU-002
// REG BO-600
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('tatva_video_item')};
CREATE TABLE {$this->getTable('tatva_video_item')} (
       video_item_id INT(10) NOT NULL AUTO_INCREMENT
     , product_id int(10) unsigned NOT NULL
	 , video_url text
     , PRIMARY KEY (video_item_id)
     , INDEX (product_id)
     , CONSTRAINT FK_sqli_video_item_1 FOREIGN KEY (product_id)
                  REFERENCES {$this->getTable('catalog_product_entity')} (entity_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

