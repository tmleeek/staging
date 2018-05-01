<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */
/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

/* @var $installer MageWorx_SearchAutocomplete_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('catalogsearch_category_fulltext')}`;
CREATE TABLE `{$installer->getTable('catalogsearch_category_fulltext')}` (
  `category_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `data_index` longtext NOT NULL,
  PRIMARY KEY (`category_id`,`store_id`),
  FULLTEXT KEY `data_index` (`data_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('cmspage_fulltext')}`;
CREATE TABLE `{$installer->getTable('cmspage_fulltext')}` (
  `page_id` int(10) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `data_index` longtext NOT NULL,
  PRIMARY KEY (`page_id`,`store_id`),
  FULLTEXT KEY `data_index` (`data_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$installer->getTable('cmspage_result')}`;
CREATE TABLE `{$installer->getTable('cmspage_result')}` (
  `query_id` int(10) unsigned NOT NULL,
  `page_id` smallint(6) NOT NULL,
  `relevance` decimal(6,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`query_id`,`page_id`),
  KEY `IDX_QUERY` (`query_id`),
  KEY `IDX_PAGE` (`page_id`),
  KEY `IDX_RELEVANCE` (`query_id`, `relevance`),
  CONSTRAINT `FK_CMSPAGE_RESULT_QUERY` FOREIGN KEY (`query_id`) REFERENCES `{$installer->getTable('catalogsearch_query')}` (`query_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CMSPAGE_RESULT_CATALOG_PRODUCT` FOREIGN KEY (`page_id`) REFERENCES `{$installer->getTable('cms_page')}` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->getConnection()->addColumn($installer->getTable('catalogsearch_query'), 'is_cmspage_processed', 'tinyint(1) DEFAULT 0 AFTER `is_processed`');
  
$installer->generateCategorySearchIndexes();
//$installer->regenerateFullIndex();

$installer->endSetup();