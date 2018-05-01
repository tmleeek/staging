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
       DROP TABLE IF EXISTS `{$this->getTable('rma/field')}`;
    ";
    $installer->run($sql);
}
$sql = "
CREATE TABLE `{$this->getTable('rma/field')}` (
    `field_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `code` VARCHAR(255) NOT NULL DEFAULT '',
    `type` VARCHAR(255) NOT NULL DEFAULT '',
    `values` TEXT,
    `description` TEXT,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` SMALLINT(5) NOT NULL DEFAULT '0',
    `is_required_staff` TINYINT(1) NOT NULL DEFAULT 0,
    `is_required_customer` TINYINT(1) NOT NULL DEFAULT 0,
    `is_visible_customer` TINYINT(1) NOT NULL DEFAULT 0,
    `is_editable_customer` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

";
$installer->run($sql);

/**                                    **/


$installer->endSetup();