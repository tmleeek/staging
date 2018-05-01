<?php
 
$installer = $this;
 
$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('purchase_supplier')}` 
ADD `sup_ftp_host` VARCHAR(255),
ADD `sup_ftp_port` INT(8),
ADD `sup_ftp_login`  VARCHAR(255),
ADD `sup_ftp_password`  VARCHAR(255),
ADD `sup_ftp_file_path`  VARCHAR(255),
ADD `sup_ftp_enabled` BOOLEAN,
ADD `sup_csv_field_separator` VARCHAR(10),
ADD `sup_csv_field_delimiter` VARCHAR(10),
ADD `sup_csv_skip_first_line` BOOLEAN,
ADD `sup_csv_sku_col_num` VARCHAR(10),
ADD `sup_csv_qty_col_num` VARCHAR(10),
ADD `sup_target_warehouse` INT(8);


CREATE TABLE IF NOT EXISTS `{$this->getTable('dropshipping_supplier_file')}` (
`dssf_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`dssf_supplier_id` INT NOT NULL ,
`dssf_product_sku` VARCHAR( 250 ) NOT NULL ,
`dssf_product_cost` DECIMAL( 8, 2 ) NOT NULL ,
`dssf_product_qty` INT( 11 ) NOT NULL
) ;


CREATE TABLE IF NOT EXISTS `{$this->getTable('dropshipping_supplier_log')}` (
`dssl_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`dssl_supplier_id` INT NOT NULL ,
`dssl_supplier_date` DATETIME ,
`dssl_supplier_log` TEXT NOT NULL,
`dssl_duration` DECIMAL (5,2) NOT NULL,
`dssl_is_error` BOOLEAN NOT NULL,
`dssl_file_name` VARCHAR( 250 ) NOT NULL
) ;


CREATE TABLE IF NOT EXISTS `{$this->getTable('dropshipping_purchase_order_supplier_log')}` (
`dsposl_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`dsposl_supplier_id` INT NOT NULL ,
`dsposl_purchase_order_id` INT NOT NULL ,
`dsposl_sales_order_id` INT NOT NULL 
) ;
");

 
$installer->endSetup();


