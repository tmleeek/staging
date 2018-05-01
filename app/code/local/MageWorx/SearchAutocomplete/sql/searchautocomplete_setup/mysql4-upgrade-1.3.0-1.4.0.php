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
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
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
DROP TABLE IF EXISTS `{$installer->getTable('searchautocomplete/cmspro_fulltext')}`;
CREATE TABLE `{$installer->getTable('searchautocomplete/cmspro_fulltext')}` (
 `news_id` int(10) unsigned NOT NULL,
 `store_id` smallint(5) unsigned NOT NULL,
 `data_index` longtext NOT NULL,
 PRIMARY KEY (`news_id`,`store_id`),
 FULLTEXT KEY `data_index` (`data_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{$installer->getTable('searchautocomplete/cmspro_result')}`;
CREATE TABLE `{$installer->getTable('searchautocomplete/cmspro_result')}` (
  `query_id` int(10) unsigned NOT NULL,
  `news_id` smallint(6) NOT NULL,
  `relevance` decimal(6,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`query_id`,`news_id`),
  KEY `IDX_CMSPRO_QUERY` (`query_id`),
  KEY `IDX_CMSPRO_PAGE` (`news_id`),
  KEY `IDX_CMSPRO_RELEVANCE` (`query_id`, `relevance`),
  CONSTRAINT `FK_CMSPRO_RESULT_QUERY` FOREIGN KEY (`query_id`) REFERENCES `{$installer->getTable('catalogsearch_query')}` (`query_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
  
$installer->endSetup();

$installer->regenerateFullIndex();