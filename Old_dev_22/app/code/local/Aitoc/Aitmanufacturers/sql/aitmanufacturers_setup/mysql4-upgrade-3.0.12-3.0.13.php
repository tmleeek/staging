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
 * 
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();
$connection = $installer->getConnection();
$connection->addColumn($this->getTable('aitmanufacturers_config'), 'include_in_navigation_menu', 'TINYINT(1) unsigned NOT NULL DEFAULT 0');

$installer->endSetup();