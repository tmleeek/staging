<?php
$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('mondialrelay_pointsrelais')};
CREATE TABLE {$this->getTable('mondialrelay_pointsrelais')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `website_id` int(11) NOT NULL default '0',
  `dest_country_id` varchar(4) NOT NULL default '0',
  `dest_region_id` int(10) NOT NULL default '0',
  `dest_zip` varchar(10) NOT NULL default '',
  `condition_name` varchar(20) NOT NULL default '',
  `condition_value` decimal(12,4) NOT NULL default '0.0000',
  `price` decimal(12,4) NOT NULL default '0.0000',
  `cost` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `dest_country` (`website_id`,`dest_country_id`,`dest_region_id`,`dest_zip`,`condition_name`,`condition_value`)
) DEFAULT CHARSET=utf8;
INSERT INTO `{$this->getTable('mondialrelay_pointsrelais')}` (`id`, `website_id`, `dest_country_id`, `dest_region_id`, `dest_zip`, `condition_name`, `condition_value`, `price`, `cost`) VALUES

(1, 1, 'FR', 0, '', 'package_weight', 0.5000, 4.2000, 4.2000),
(2, 1, 'FR', 0, '', 'package_weight', 1.0000, 4.2000, 4.2000),
(3, 1, 'FR', 0, '', 'package_weight', 2.0000, 5.5000, 5.5000),
(4, 1, 'FR', 0, '', 'package_weight', 3.0000, 6.2000, 6.2000),
(5, 1, 'FR', 0, '', 'package_weight', 5.0000, 7.5000, 7.5000),
(6, 1, 'FR', 0, '', 'package_weight', 7.0000, 9.6000, 9.6000),
(7, 1, 'FR', 0, '', 'package_weight', 10.0000, 11.9500, 11.9500),
(8, 1, 'FR', 0, '', 'package_weight', 15.0000, 14.3500, 14.3500),
(9, 1, 'FR', 0, '', 'package_weight', 20.0000, 17.9500, 17.9500),
(11, 1, 'BE', 0, '', 'package_weight', 0.5000, 4.2000, 4.2000),
(12, 1, 'BE', 0, '', 'package_weight', 1.0000, 4.8000, 4.8000),
(13, 1, 'BE', 0, '', 'package_weight', 2.0000, 5.5000, 5.5000),
(14, 1, 'BE', 0, '', 'package_weight', 3.0000, 6.2000, 6.2000),
(15, 1, 'BE', 0, '', 'package_weight', 5.0000, 7.5000, 7.5000),
(16, 1, 'BE', 0, '', 'package_weight', 7.0000, 9.6000, 9.6000),
(17, 1, 'BE', 0, '', 'package_weight', 10.0000, 11.9500, 11.9500),
(18, 1, 'BE', 0, '', 'package_weight', 15.0000, 14.3500, 14.3500),
(19, 1, 'BE', 0, '', 'package_weight', 20.0000, 17.9500, 17.9500);
");

$installer->endSetup();
