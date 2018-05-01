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
$installer = $this;

$installer->startSetup();

$setup = Mage::getModel('eav/entity_setup', 'core_setup');
$setup->updateAttribute('catalog_product', 'aitmanufacturers_sort', 'used_for_sort_by', 1);

$installer->endSetup();