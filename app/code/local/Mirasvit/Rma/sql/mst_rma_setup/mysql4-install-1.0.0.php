<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


$installer = $this;
$installer->startSetup();

if (Mage::registry('mst_allow_drop_tables')) {
    $sql = "
       DROP TABLE IF EXISTS `{$this->getTable('rma/rma')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rma/rma_store')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rma/item')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rma/reason')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rma/status')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rma/resolution')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rma/condition')}`;
       DROP TABLE IF EXISTS `{$this->getTable('rma/comment')}`;
    ";
    $installer->run($sql);
}
$sql = "
CREATE TABLE `{$this->getTable('rma/rma')}` (
    `rma_id` int(11) NOT NULL AUTO_INCREMENT,
    `increment_id` VARCHAR(255) NOT NULL DEFAULT '',
    `guest_id` VARCHAR(255) NOT NULL DEFAULT '',
    `firstname` VARCHAR(255) NOT NULL DEFAULT '',
    `lastname` VARCHAR(255) NOT NULL DEFAULT '',
    `company` VARCHAR(255) NOT NULL DEFAULT '',
    `telephone` VARCHAR(255) NOT NULL DEFAULT '',
    `email` VARCHAR(255) NOT NULL DEFAULT '',
    `street` VARCHAR(255) NOT NULL DEFAULT '',
    `city` VARCHAR(255) NOT NULL DEFAULT '',
    `region` VARCHAR(255) NOT NULL DEFAULT '',
    `region_id` INT(11),
    `country_id` VARCHAR(255) NOT NULL DEFAULT '',
    `postcode` VARCHAR(255) NOT NULL DEFAULT '',
    `customer_id` int(10) unsigned,
    `order_id` int(10) unsigned NOT NULL,
    `status_id` INT(11) NOT NULL,
    `store_id` SMALLINT(5) unsigned NOT NULL,
    `tracking_code` VARCHAR(255) NOT NULL DEFAULT '',
    `is_resolved` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    KEY `fk_rma_rma_customer_id` (`customer_id`),
    CONSTRAINT `mst_69a8b395f24a094533cf153e8e7858f8` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    KEY `fk_rma_rma_order_id` (`order_id`),
    CONSTRAINT `mst_69eaead6204d31508d9b7cddf50050ea` FOREIGN KEY (`order_id`) REFERENCES `{$this->getTable('sales/order')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rma_rma_status_id` (`status_id`),
    CONSTRAINT `mst_c06ac63c728d57ffa50c0764ad0b9668` FOREIGN KEY (`status_id`) REFERENCES `{$this->getTable('rma/status')}` (`status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rma_rma_store_id` (`store_id`),
    CONSTRAINT `mst_dc1a175a9f3b92e40f0fc89072824bb7` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`rma_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('rma/rma_store')}` (
    `rma_store_id` int(11) NOT NULL AUTO_INCREMENT,
    `rs_rma_id` INT(11) NOT NULL,
    `rs_store_id` SMALLINT(5) unsigned NOT NULL,
    KEY `fk_rma_rma_store_rma_id` (`rs_rma_id`),
    CONSTRAINT `mst_a4d8c322882c1c00f6a47d496c2d2b53` FOREIGN KEY (`rs_rma_id`) REFERENCES `{$this->getTable('rma/rma')}` (`rma_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rma_rma_store_store_id` (`rs_store_id`),
    CONSTRAINT `mst_ab18e99ea1f412d02e4affdacfbb3cf7` FOREIGN KEY (`rs_store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`rma_store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('rma/item')}` (
    `item_id` int(11) NOT NULL AUTO_INCREMENT,
    `rma_id` INT(11) NOT NULL,
    `product_id` INT(11),
    `order_item_id` INT(11),
    `reason_id` INT(11),
    `resolution_id` INT(11),
    `condition_id` INT(11),
    `qty_requested` INT(11),
    `qty_returned` INT(11),
    `created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    KEY `fk_rma_item_rma_id` (`rma_id`),
    CONSTRAINT `mst_de6add712108ad78167f577b6c45e61f` FOREIGN KEY (`rma_id`) REFERENCES `{$this->getTable('rma/rma')}` (`rma_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rma_item_reason_id` (`reason_id`),
    CONSTRAINT `mst_aa079f3184daab6fc043a86191a7dc18` FOREIGN KEY (`reason_id`) REFERENCES `{$this->getTable('rma/reason')}` (`reason_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    KEY `fk_rma_item_resolution_id` (`resolution_id`),
    CONSTRAINT `mst_fa11b611a0646649046b3f7ad2a7d094` FOREIGN KEY (`resolution_id`) REFERENCES `{$this->getTable('rma/resolution')}` (`resolution_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    KEY `fk_rma_item_condition_id` (`condition_id`),
    CONSTRAINT `mst_9c5f65d4b052b10b78d1fac88f9d101d` FOREIGN KEY (`condition_id`) REFERENCES `{$this->getTable('rma/condition')}` (`condition_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    PRIMARY KEY (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('rma/reason')}` (
    `reason_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `sort_order` SMALLINT(5) NOT NULL DEFAULT '0',
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`reason_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('rma/status')}` (
    `status_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `sort_order` SMALLINT(5) NOT NULL DEFAULT '0',
    `is_rma_resolved` TINYINT(1) NOT NULL DEFAULT 0,
    `customer_message` TEXT,
    `admin_message` TEXT,
    `history_message` TEXT,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`status_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('rma/resolution')}` (
    `resolution_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `sort_order` SMALLINT(5) NOT NULL DEFAULT '0',
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`resolution_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('rma/condition')}` (
    `condition_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `sort_order` SMALLINT(5) NOT NULL DEFAULT '0',
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`condition_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('rma/comment')}` (
    `comment_id` int(11) NOT NULL AUTO_INCREMENT,
    `rma_id` INT(11) NOT NULL,
    `user_id` ".(Mage::getVersion() >= '1.6.0.0'? 'int(10)': 'mediumint(11)')." unsigned,
    `customer_id` int(10) unsigned,
    `customer_name` VARCHAR(255) NOT NULL DEFAULT '',
    `text` TEXT,
    `is_html` TINYINT(1) NOT NULL DEFAULT 0,
    `is_visible_in_frontend` TINYINT(1) NOT NULL DEFAULT 0,
    `is_customer_notified` TINYINT(1) NOT NULL DEFAULT 0,
    `status_id` INT(11),
    `created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    `updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
    KEY `fk_rma_comment_rma_id` (`rma_id`),
    CONSTRAINT `mst_e53ea47d9d7ec6e779ba0de3ff8b78b2` FOREIGN KEY (`rma_id`) REFERENCES `{$this->getTable('rma/rma')}` (`rma_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    KEY `fk_rma_comment_user_id` (`user_id`),
    CONSTRAINT `mst_5958c13ea82bb0cab3612e12eaab94db` FOREIGN KEY (`user_id`) REFERENCES `{$this->getTable('admin/user')}` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    KEY `fk_rma_comment_customer_id` (`customer_id`),
    CONSTRAINT `mst_4c6f91e9e296b0641c1881e543f3cf09` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    KEY `fk_rma_comment_status_id` (`status_id`),
    CONSTRAINT `mst_faa0dc357796a205d32cce27234442fd` FOREIGN KEY (`status_id`) REFERENCES `{$this->getTable('rma/status')}` (`status_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

";
$installer->run($sql);

$sql = "
   INSERT INTO `{$this->getTable('rma/reason')}` (name,is_active,sort_order) VALUES ('Out of Service','1','10');
   INSERT INTO `{$this->getTable('rma/reason')}` (name,is_active,sort_order) VALUES ('Don\'t like','1','20');
   INSERT INTO `{$this->getTable('rma/reason')}` (name,is_active,sort_order) VALUES ('Wrong color','1','30');
   INSERT INTO `{$this->getTable('rma/reason')}` (name,is_active,sort_order) VALUES ('Wrong size','1','40');
   INSERT INTO `{$this->getTable('rma/reason')}` (name,is_active,sort_order) VALUES ('Other','1','50');

   INSERT INTO `{$this->getTable('rma/condition')}` (name,is_active,sort_order) VALUES ('Unopened','1','10');
   INSERT INTO `{$this->getTable('rma/condition')}` (name,is_active,sort_order) VALUES ('Opened','1','20');
   INSERT INTO `{$this->getTable('rma/condition')}` (name,is_active,sort_order) VALUES ('Damaged','1','30');

   INSERT INTO `{$this->getTable('rma/resolution')}` (name,is_active,sort_order) VALUES ('Exchange','1','10');
   INSERT INTO `{$this->getTable('rma/resolution')}` (name,is_active,sort_order) VALUES ('Refund','1','20');
   INSERT INTO `{$this->getTable('rma/resolution')}` (name,is_active,sort_order) VALUES ('Store Credit','1','30');

   INSERT INTO `{$this->getTable('rma/status')}` (name,is_active,sort_order,is_rma_resolved,customer_message,history_message,admin_message) VALUES ('Pending Approval','1','10','0','<p>Your Return request has been received. You will be notified when your request is reviewed.</p>
<p>You can check status of your Return request <a href=\"[rma_guest_url]\">here</a>.</p>
','<p>Return request has been received. You will be notified when your request is reviewed.</p>','<p>
RMA #[rma_increment_id] has been created.
</p>
<p>
Customer: [customer_name]<br>
Order: #[order_increment_id]<br>
</p>
[rma_items]');
   INSERT INTO `{$this->getTable('rma/status')}` (name,is_active,sort_order,is_rma_resolved,customer_message,history_message) VALUES ('Approved','1','20','1','<p>
Your Return request has been approved.
</p>
<p>Please, print <a href=\"[rma_guest_print_url]\">Return Shipping Label</a> and send package to:</p>
<p>[rma_return_address]</p>
<p>You can check history of your Return request <a href=\"[rma_guest_url]\">here</a>.</p>
','<p>
Your Return request has been approved.
</p>
<p>Please, print <a href=\"[rma_guest_print_url]\">Return Shipping Label</a> and send package to:</p>
<p>[rma_return_address]</p>
');
   INSERT INTO `{$this->getTable('rma/status')}` (name,is_active,sort_order,is_rma_resolved,customer_message,history_message) VALUES ('Rejected','1','30','0','<p>
Your Return request has been rejected.
</p>
<p>You can check history of your Return request <a href=\"[rma_guest_url]\">here</a>.</p>
','
<p>
Return request has been rejected.
</p>
');
   INSERT INTO `{$this->getTable('rma/status')}` (name,is_active,sort_order,is_rma_resolved,customer_message,history_message) VALUES ('Closed','1','40','0','<p>
Your Return request has been closed.
</p>
<p>You can check history of your Return request <a href=\"[rma_guest_url]\">here</a>.</p>
','
<p>
Return request has been closed.
</p>
');

   ALTER TABLE `{$this->getTable('rma/rma')}` ADD UNIQUE INDEX `increment_id` (`increment_id`);
";
$installer->run($sql);

/**                                    **/


$installer->endSetup();