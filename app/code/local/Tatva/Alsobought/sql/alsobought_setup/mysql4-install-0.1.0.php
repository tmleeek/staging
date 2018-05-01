<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Sqli
 * @package    Sqli_Alsobought
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->run("

# Insertion du nouveau type d'association
INSERT INTO {$this->getTable('catalog_product_link_type')} (`link_type_id`, `code`) VALUES (100, 'also_bought');

# Insertion de l'attribut 'Nombre de commandes'
INSERT INTO {$this->getTable('catalog_product_link_attribute')} (`link_type_id`, `product_link_attribute_code`, `data_type`) VALUES (100, 'position', 'int');

");

$installer->endSetup();
