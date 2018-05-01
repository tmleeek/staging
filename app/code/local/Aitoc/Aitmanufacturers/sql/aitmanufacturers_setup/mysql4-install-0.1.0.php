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

-- DROP TABLE IF EXISTS {$this->getTable('aitmanufacturers')};
CREATE TABLE IF NOT EXISTS {$this->getTable('aitmanufacturers')} (
  `id` INTEGER(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `manufacturer_id` INTEGER(10) UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `content` TEXT NOT NULL,
  `meta_keywords` TEXT NOT NULL,
  `meta_description` TEXT NOT NULL,
  `url_key` VARCHAR(255) NOT NULL DEFAULT '',
  `root_template` VARCHAR(255) NOT NULL DEFAULT '',
  `layout_update_xml` TEXT NOT NULL,
  `status` SMALLINT(6) NOT NULL DEFAULT '0',
  `sort_order` SMALLINT(6) NOT NULL,
  `created_time` DATETIME DEFAULT NULL,
  `update_time` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url_key` (`url_key`),
  KEY `manufacturer_id` (`manufacturer_id`),
  CONSTRAINT `FK_AITMANUFACTURERS_EAV_ATTRIBUTE_OPTION_OPTION` FOREIGN KEY (`manufacturer_id`) REFERENCES {$this->getTable('eav_attribute_option')} (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('aitmanufacturers_stores')};
CREATE TABLE IF NOT EXISTS {$this->getTable('aitmanufacturers_stores')} (
  `manufacturers_id` INTEGER(11) UNSIGNED NOT NULL,
  `store_id` SMALLINT(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`manufacturers_id`, `store_id`),
  KEY `FK_AITMANUFACTURERS_STORES_STORE` (`store_id`),
  CONSTRAINT `FK_AITMANUFACTURERS_STORES_MANUFACTURER` FOREIGN KEY (`manufacturers_id`) REFERENCES {$this->getTable('aitmanufacturers')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_AITMANUFACTURERS_STORES_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();