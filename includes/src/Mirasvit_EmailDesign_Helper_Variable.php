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
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_EmailDesign_Helper_Variable extends Mage_Core_Helper_Abstract
{
    public function getStoreVariables($storeId)
    {
        $store = Mage::app()->getStore($storeId);

        $vars  = array(
            'logo_url'      => $this->_getLogoUrl($store),
            'logo_alt'      => $this->_getLogoAlt($store),
            'store_name'    => $this->getStoreName($store),
            'store_phone'   => $this->getStorePhone($store),
            'store_address' => $this->getStoreAddress($store),
            'facebook_url'  => $this->getFacebookUrl($store),
            'twitter_url'   => $this->getTwitterUrl($store),
            'current_year'  => date('Y'),
            'store_url'     => $store->getBaseUrl(),
            'store_email'   => Mage::getStoreConfig('trans_email/ident_general/email'),
        );

        return $vars;
    }


    protected function _getLogoUrl($store)
    {

        $fileName = Mage::getStoreConfig('design/header/logo_src');
        $path     = Mage::getDesign()->getSkinUrl($fileName);

        return $path;

        $fileName = $store->getConfig('design/email/logo');

        if ($fileName) {
            $uploadDir    = Mage_Adminhtml_Model_System_Config_Backend_Email_Logo::UPLOAD_DIR;
            $fullFileName = Mage::getBaseDir('media').DS.$uploadDir.DS.$fileName;
            if (file_exists($fullFileName)) {
                return Mage::getBaseUrl('media').$uploadDir.DS.$fileName;
            }
        }

        return Mage::getDesign()->getSkinUrl('images/logo_email.gif');
    }

    protected function _getLogoAlt($store)
    {
        $alt   = $store->getConfig('design/email/logo_alt');

        if ($alt) {
            return $alt;
        }

        return $store->getFrontendName();
    }

    protected function getStoreName($store)
    {
        return $store->getFrontendName();
    }

    protected function getStorePhone($store)
    {
        return Mage::getStoreConfig('general/store_information/phone', $store);
    }

    protected function getStoreAddress($store)
    {
        return Mage::getStoreConfig('general/store_information/address', $store);
    }

    protected function getFacebookUrl($store)
    {
        return Mage::getStoreConfig('trigger_email/info/facebook_url', $store);
    }

    protected function getTwitterUrl($store)
    {
        return Mage::getStoreConfig('trigger_email/info/twitter_url', $store);
    }
}