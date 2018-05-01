<?php
/**
 * MagestyApps
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
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.magestyapps.com for more information or
 * send an email to office@magestyapps.com .
 *
 * @category    MagestyApps
 * @package     MagestyApps_AdvancedBreadcrumbs
 * @copyright   Copyright (c) 2016 MagestyApps (https://www.magestyapps.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->addAttribute('catalog_product', 'default_breadcrumbs', array(
    'type'      => 'int',
    'label'     => 'Default Breadcrumbs Path',
    'input'     => 'select',
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'group'     => 'Advanced Breadcrumbs',
    'required' => false
));

$installer->endSetup();