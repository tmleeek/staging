<?php

$installer = $this;

$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS `{$this->getTable('optionimages/product_option_type_image')}`;
    CREATE TABLE `{$this->getTable('optionimages/product_option_type_image')}` (
      `option_type_image_id` int(10) unsigned NOT NULL auto_increment,
      `option_type_id` int(10) unsigned NOT NULL default '0',
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `image` varchar(255) NOT NULL default '',
      PRIMARY KEY (`option_type_image_id`),
      KEY `OPTIONIMAGES_PRODUCT_OPTION_TYPE_IMAGE_OPTION` (`option_type_id`),
      KEY `OPTIONIMAGES_PRODUCT_OPTION_TYPE_IMAGE_STORE` (`store_id`),
      CONSTRAINT `FK_CATALOG_PRODUCT_OPTION_TYPE_IMAGE_OPTION` FOREIGN KEY (`option_type_id`) REFERENCES `{$this->getTable('catalog/product_option_type_value')}` (`option_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_CATALOG_PRODUCT_OPTION_TYPE_IMAGE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      KEY `IDX_CATALOG_PRODUCT_OPTION_TYPE_IMAGE_SI_OTI` (`store_id`, `option_type_id`)
    )ENGINE=InnoDB default CHARSET=utf8;

    ");

$installer->endSetup(); 