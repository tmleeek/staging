<?php

$installer = $this;

$installer->startSetup();

Mage::helper('rewards/mysql4_install')->attemptQuery($installer, "
    ALTER TABLE `{$this->getTable('rewards/transfer')}`
      ADD COLUMN `source_reference_id` INT(11) NOT NULL
");

// only if it's an update of Sweet Tooth, make sure we keep allow_points_summary_email system setting unchanged
if (! $installer->getIsFirstInstall()) {
    $allow = Mage::getModel('core/config_data')->load('rewards/display/allow_points_summary_email', 'path')->getValue();
    // check if no value in DB, meaning that they still use default (option enabled), so make sure to keep it
    if (is_null($allow)) {
        Mage::getConfig()->saveConfig('rewards/display/allow_points_summary_email', 1);
    }
}

/**
 * This script will update customer attribute 'rewards_points_notification' and set it's default value to 1
 * ST-1761
 */
$eavConfig = Mage::getSingleton('eav/config');

$attribute = $eavConfig->getAttribute('customer', 'rewards_points_notification');
$attribute->setDefaultValue(1)
    ->save();

// also forcing all customers to get notifications, after this they'll choose whether to get or not them
$table = $attribute->getBackend()->getTable();
$attributeId = $attribute->getAttributeId();
Mage::helper('rewards/mysql4_install')->attemptQuery($installer, "
    UPDATE `{$table}` AS `e`
    SET `e`.`value` = 1
    WHERE (`e`.`attribute_id` = {$attributeId})
");

// Clear cache.
Mage::helper( 'rewards/mysql4_install' )->prepareForDb();

$installer->endSetup();
