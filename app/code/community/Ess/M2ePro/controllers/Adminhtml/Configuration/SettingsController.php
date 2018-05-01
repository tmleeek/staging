<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Configuration_SettingsController
    extends Ess_M2ePro_Controller_Adminhtml_Configuration_MainController
{
    //########################################

    public function saveAction()
    {
        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue(
            '/view/','show_products_thumbnails',
            (int)$this->getRequest()->getParam('products_show_thumbnails')
        );
        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue(
            '/view/', 'show_block_notices',
            (int)$this->getRequest()->getParam('block_notices_show')
        );

        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue(
            '/product/force_qty/', 'mode',
            (int)$this->getRequest()->getParam('force_qty_mode')
        );

        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue(
            '/product/force_qty/', 'value',
            (int)$this->getRequest()->getParam('force_qty_value')
        );

        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue(
            '/magento/attribute/','price_type_converting',
            (int)$this->getRequest()->getParam('price_convert_mode')
        );

        Mage::helper('M2ePro/Module')->getSynchronizationConfig()->setGroupValue(
            '/global/magento_products/inspector/', 'mode',
            (int)$this->getRequest()->getParam('inspector_mode')
        );

        $this->_getSession()->addSuccess(
            Mage::helper('M2ePro')->__('The global Settings have been successfully saved.')
        );

        $this->_redirectUrl($this->_getRefererUrl());
    }

    //########################################

    public function restoreBlockNoticesAction()
    {
        foreach ($_COOKIE as $name => $value) {
            if (strpos($name,'m2e_bn_') !== false || strpos($name,'_skip_save_confirmation') !== false) {
                setcookie($name, '', 0, '/');
            }
        }

        $collection = Mage::getModel('M2ePro/Listing')->getCollection();

        foreach ($collection as $listing) {
            /** @var $listing Ess_M2ePro_Model_Listing */

            $additionalData = $listing->getSettings('additional_data');

            if ($listing->isComponentModeEbay()) {
                unset($additionalData['show_settings_step']);
                unset($additionalData['mode_same_category_data']);
            }

            if ($listing->isComponentModeAmazon()) {
                unset($additionalData['show_new_asin_step']);
            }

            $listing->setSettings('additional_data', $additionalData);
        }

        $this->_getSession()->addSuccess(Mage::helper('M2ePro')->__('All Help Blocks were restored.'));
        $this->_redirectUrl($this->_getRefererUrl());
    }

    //########################################
}