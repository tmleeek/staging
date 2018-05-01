<?php
$installer = $this;

$installer->startSetup();


Mage::helper('rewards/mysql4_install')->attemptQuery($installer, "
	ALTER TABLE `{$this->getTable('sales_flat_quote_item')}`
		MODIFY COLUMN `row_total_before_redemptions` DECIMAL(12,4) DEFAULT NULL,
		MODIFY COLUMN `row_total_before_redemptions_incl_tax` DECIMAL(12,4) DEFAULT NULL,
		MODIFY COLUMN `row_total_after_redemptions` DECIMAL(12,4) DEFAULT NULL,
		MODIFY COLUMN `row_total_after_redemptions_incl_tax` DECIMAL(12,4) DEFAULT NULL
");

Mage::helper('rewards/mysql4_install')->attemptQuery($installer, "
	ALTER TABLE `{$this->getTable('sales_flat_order_item')}`
		MODIFY COLUMN `row_total_before_redemptions` DECIMAL(12,4) DEFAULT NULL,
		MODIFY COLUMN `row_total_before_redemptions_incl_tax` DECIMAL(12,4) DEFAULT NULL,
		MODIFY COLUMN `row_total_after_redemptions` DECIMAL(12,4) DEFAULT NULL,
		MODIFY COLUMN `row_total_after_redemptions_incl_tax` DECIMAL(12,4) DEFAULT NULL
");


$installer->endSetup(); 
