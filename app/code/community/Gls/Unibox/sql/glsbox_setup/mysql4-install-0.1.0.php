<?php
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('gls_unibox_shipment')} (
  `id` int(10) unsigned NOT NULL auto_increment,
  `shipment_id` int(30) NOT NULL,
  `weight` int(10) NOT NULL,

  `kundennummer` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `customerid` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `contactid` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `depotcode` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `depotnummer` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,    
  `storniert` tinyint(4) default 0, 
  
  `paketnummer` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `service` varchar(1000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `additional_service` varchar(1000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  
  `gls_message` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `notes` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,  
  `created_at` timestamp NOT NULL default current_timestamp,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `paketnummer` (`paketnummer`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='GLS Sendungen';

CREATE TABLE IF NOT EXISTS {$this->getTable('gls_unibox_client')} (
  `id` int(10) NOT NULL auto_increment,
  `display_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default '',
  `kundennummer` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, 
  `customerid` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `contactid` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `depotcode` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `depotnummer` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,  
  `status` tinyint(4),
  `notes` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='GLS Mandanten';

REPLACE INTO {$this->getTable('core_config_data')} (scope, scope_id, path, value) VALUES ('default', 0, 'glsbox/general/install-date', NOW());
");
$installer->endSetup();