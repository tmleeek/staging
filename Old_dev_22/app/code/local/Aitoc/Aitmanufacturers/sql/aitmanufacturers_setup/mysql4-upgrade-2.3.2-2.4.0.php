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

$this->addAttribute('catalog_product', 'aitmanufacturers_sort', array(
	'type'						=> 'int',
	'label'						=> 'Position',
	'required'					=> 0,
	'visible'					=> 0,
	'default'					=> 9999,
	'global'					=> 0,
	'is_configurable'			=> 0,
	'used_for_price_rules'		=> 0,
));

$installer->endSetup();