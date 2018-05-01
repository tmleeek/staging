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
 * @author yahnenko
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();

$modules = Mage::getModel('aitsys/aitsys')->getAitocModuleList();

if (isset($modules['Aitoc_Aitsplash'])) 
{
	$installer->run("
    	ALTER TABLE {$this->getTable('aitmanufacturers')} 
        	ADD `aitsplash_mode` INT NOT NULL COMMENT 'Splash Screen Mode',
        	ADD `aitsplash_page` INT NOT NULL COMMENT 'Splash Screen CMS Page',
        	ADD `aitsplash_ageverify` INT NOT NULL COMMENT 'Age Verification Block',
        	ADD `aitsplash_agreement` INT NOT NULL COMMENT 'Agreement Block';
	");
}

	$installer->run("
        CREATE TABLE IF NOT EXISTS {$this->getTable('aitmanufacturers_config')} (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `is_active` tinyint(1) unsigned NOT NULL,
          `scope` enum('default','website','store','config') NOT NULL,
          `scope_id` int(11) unsigned NOT NULL,
          `url_prefix` varchar(40) NOT NULL,
          `url_pattern` varchar(40) NOT NULL,
          `attribute_code` varchar(50) NOT NULL,
          `columns_num` smallint(3) NOT NULL,
          `brief_num` mediumint(5) NOT NULL,
          `show_brands_withproducts_only` tinyint(1) unsigned NOT NULL,
          `show_categories_as_tree` tinyint(1) unsigned NOT NULL,
          `show_brands_from_category_only` tinyint(1) unsigned NOT NULL,
          `show_logo` tinyint(1) unsigned NOT NULL,
          `show_link` tinyint(1) unsigned NOT NULL,
          `show_brands_in_sitemap` tinyint(1) unsigned NOT NULL,
          `show_brief_image` tinyint(1) unsigned NOT NULL,
          `show_list_image` tinyint(1) unsigned NOT NULL,
          `rename_pic` tinyint(1) unsigned NOT NULL,
          `layered_navigation` tinyint(1) unsigned NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
	");



$installer->endSetup();