<?php
$installer = $this;
$installer->startSetup();
$installer->run("
Alter TABLE {$this->getTable('gls_unibox_client')}
add `numcircle_standard_start` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default '',
add `numcircle_standard_end` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default '',
add `numcircle_express_start` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default '',
add `numcircle_express_end` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default '';
");
$installer->endSetup();