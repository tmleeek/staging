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
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
$connection = $installer->getConnection();
$connection->addColumn($this->getTable('aitmanufacturers'), 'list_image', 'VARCHAR(255) NOT NULL AFTER `small_logo`');
$connection->addColumn($this->getTable('aitmanufacturers'), 'show_brief_image', 'TINYINT(1) NOT NULL  DEFAULT "1" AFTER `list_image`');
$connection->addColumn($this->getTable('aitmanufacturers'), 'show_list_image', 'TINYINT(1) NOT NULL DEFAULT "1" AFTER `show_brief_image`');

$installer->endSetup();