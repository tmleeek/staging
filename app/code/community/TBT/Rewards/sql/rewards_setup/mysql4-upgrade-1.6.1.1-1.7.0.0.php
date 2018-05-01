<?php
$installer = $this;

$installer->startSetup();


Mage::helper('rewards/mysql4_install')->attemptQuery($installer, "
    ALTER TABLE `{$this->getTable('salesrule')}`
        ADD COLUMN `points_discount_action` VARCHAR(32) NULL DEFAULT NULL AFTER `points_action`,
        ADD COLUMN `points_discount_amount` DECIMAL(12,4) NOT NULL DEFAULT '0.0000' AFTER `points_amount`;
");

Mage::helper('rewards/mysql4_install')->attemptQuery($installer, "
	UPDATE `{$this->getTable('salesrule')}` SET
		`points_discount_action` = `simple_action`,
		`points_discount_amount` = `discount_amount`,
		`simple_action` = NULL,
		`discount_amount` = 0.0
	WHERE `points_action` IS NOT NULL
		AND `simple_action` IS NOT NULL;
");


// Clear cache.
Mage::helper( 'rewards/mysql4_install' )->prepareForDb();

$installer->endSetup(); 
