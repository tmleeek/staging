<?php

$installer = $this;

$installer->startSetup();

$setup = new Mage_Customer_Model_Entity_Setup('core_setup');

$setup->updateAttribute('customer', 'rewardsref_notify_on_referral', 'source_model', "eav/entity_attribute_source_boolean");

Mage::helper ( 'rewards/mysql4_install' )->attemptQuery ( $installer, "
ALTER TABLE `{$this->getTable('rewardsref_referral')}` DROP INDEX email;
");

$installer->endSetup();