<?php

$installer = $this;

$installer->startSetup();

// this column is no longer needed as we use rewards/catalogrule_product table since 1.6.0.6 and conflicts with MEE 1.13
Mage::helper('rewards/mysql4_install')->dropColumns($installer, $this->getTable('catalogrule_product_price'),
    array(
        "`rules_hash`"
    ) );

$installer->endSetup();
