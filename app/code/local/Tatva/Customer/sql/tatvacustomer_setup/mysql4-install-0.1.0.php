<?php

    $installer = $this;

    $installer->startSetup();

    $installer->updateAttribute('customer_address', 'telephone', 'is_required', false);

    $installer->addAttribute('customer_address', 'mobilephone', array('type'=> 'varchar' , 'label'=> 'Mobilephone'));

    $this->addAttribute('customer_address', 'mobilephone', array(
        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Mobilephone',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 0,
        'sort_order' => 140,
        'visible_on_front' => 1
    ));
    Mage::getSingleton('eav/config')
        ->getAttribute('customer_address', 'mobilephone')
        ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address','checkout_register'))
        ->save();

    $installer->run("
        ALTER TABLE {$this->getTable('sales_flat_quote_address')} ADD COLUMN `mobilephone` VARCHAR(255) DEFAULT NULL AFTER `telephone`;
        ALTER TABLE {$this->getTable('sales_flat_order_address')} ADD COLUMN `mobilephone` VARCHAR(255) DEFAULT NULL AFTER `telephone`;
    ");

    $installer->endSetup();

?>