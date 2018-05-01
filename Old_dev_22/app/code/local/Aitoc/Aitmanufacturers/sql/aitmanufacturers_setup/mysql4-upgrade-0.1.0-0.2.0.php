<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

$installer = $this;

$installer->startSetup();


$installer->run("

ALTER TABLE {$this->getTable('aitmanufacturers_stores')} DROP FOREIGN KEY `FK_AITMANUFACTURERS_STORES_MANUFACTURER`;

ALTER TABLE {$this->getTable('aitmanufacturers_stores')} CHANGE COLUMN `manufacturers_id` `id` INTEGER(11) UNSIGNED NOT NULL;

ALTER TABLE {$this->getTable('aitmanufacturers_stores')} ADD CONSTRAINT `FK_AITMANUFACTURERS_STORES_MANUFACTURER` FOREIGN KEY (`id`)
	REFERENCES {$this->getTable('aitmanufacturers')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE;");

$connection = $this->getConnection();
$connection->addColumn($this->getTable('aitmanufacturers_stores'), 'manufacturer_id', 'INTEGER(11) UNSIGNED NOT NULL AFTER `id`');
$connection->addColumn($this->getTable('aitmanufacturers'), 'small_logo', 'VARCHAR(255) NOT NULL AFTER `title`');
$connection->addColumn($this->getTable('aitmanufacturers'), 'featured', 'SMALLINT(2) UNSIGNED NOT NULL DEFAULT "0" AFTER `layout_update_xml`');

$installer->run("
UPDATE  {$this->getTable('aitmanufacturers_stores')},  {$this->getTable('aitmanufacturers')} SET {$this->getTable('aitmanufacturers_stores')}.`manufacturer_id` = {$this->getTable('aitmanufacturers')}.`manufacturer_id`
    WHERE {$this->getTable('aitmanufacturers_stores')}.id = {$this->getTable('aitmanufacturers')}.`id`;
");


$installer->endSetup();