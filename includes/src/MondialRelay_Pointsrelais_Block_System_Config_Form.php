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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * System config form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MondialRelay_Pointsrelais_Block_System_Config_Form extends Mage_Adminhtml_Block_System_Config_Form
{   
 
    /**
     * Enter description here...
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'export'        => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_export'),
            'import'        => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_import'),
            'allowspecific' => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_select_allowspecific'),
            'image'         => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_image'),
            'export_pointsrelais'         => Mage::getConfig()->getBlockClassName('pointsrelais/system_config_form_field_exportpointsrelais'),
            'export_pointsrelaiscd'         => Mage::getConfig()->getBlockClassName('pointsrelais/system_config_form_field_exportpointsrelaiscd'),
            'export_pointsrelaisld1'         => Mage::getConfig()->getBlockClassName('pointsrelais/system_config_form_field_exportpointsrelaisld1'),
            'export_pointsrelaislds'         => Mage::getConfig()->getBlockClassName('pointsrelais/system_config_form_field_exportpointsrelaislds')
        );
    }

    }
