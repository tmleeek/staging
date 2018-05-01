<?php
/**
 * WDCA - Sweet Tooth
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the WDCA SWEET TOOTH POINTS AND REWARDS
 * License, which extends the Open Software License (OSL 3.0).
 * The Sweet Tooth License is available at this URL:
 *      https://www.sweettoothrewards.com/terms-of-service
 * The Open Software License is available at this URL:
 *      http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * By adding to, editing, or in any way modifying this code, WDCA is
 * not held liable for any inconsistencies or abnormalities in the
 * behaviour of this code.
 * By adding to, editing, or in any way modifying this code, the Licensee
 * terminates any agreement of support offered by WDCA, outlined in the
 * provided Sweet Tooth License.
 * Upon discovery of modified code in the process of support, the Licensee
 * is still held accountable for any and all billable time WDCA spent
 * during the support process.
 * WDCA does not guarantee compatibility with any other framework extension.
 * WDCA is not responsbile for any inconsistencies or abnormalities in the
 * behaviour of this code if caused by other framework extension.
 * If you did not receive a copy of the license, please send an email to
 * support@sweettoothrewards.com or call 1.855.699.9322, so we can send you a copy
 * immediately.
 *
 * @category   [TBT]
 * @package    [TBT_Rewards]
 * @copyright  Copyright (c) 2014 Sweet Tooth Inc. (http://www.sweettoothrewards.com
 */

/**
 * Helper Data
 *
 * @category   TBT
 * @package    TBT_Rewardssocial
 * * @author     Sweet Tooth Inc. <support@sweettoothrewards.com>
 */
class TBT_Rewardssocial_Helper_Data extends Mage_Core_Helper_Abstract {



    public function getTextWithLinks($text, $link_key, $url, $options=array()) {
        if(isset($options['target'])) {
            $a_start = "<a href='{$url}' target='{$options['target']}'>";
        } else {
            $a_start = "<a href='{$url}'>";
        }
        $text = str_replace("[{$link_key}]", $a_start, $text);
        $text = str_replace("[/{$link_key}]", "</a>", $text);
        return $text;
    }

    /**
     * Check is module exists and enabled in global config.
     *
     * @param string $moduleName the full module name, example Mage_Core
     * @return boolean
     */
    public function isModuleEnabled($moduleName = null)
    {
        if ($moduleName === null) {
            $moduleName = $this->_getModuleName();
        }

        if (!Mage::getConfig()->getNode('modules/' . $moduleName)) {
            return false;
        }

        $isActive = Mage::getConfig()->getNode('modules/' . $moduleName . '/active');
        if (!$isActive || !in_array((string)$isActive, array('true', '1'))) {
            return false;
        }
        return true;
    }

    /**
     * Returns Sweet Tooth system configuration URL
     * @return string backend system configuration url for Sweet Tooth
     */
    public function getConfigUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit', array('section' => 'rewards'));
    }

    /**
     * Returns Retail Evolved Like system configuration URL
     * @return string backend system configuration url for the Retail Evolved Like extension module screen
     */
    public function getREConfigUrl()
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/system_config/edit', array('section' => 'evlike'));
    }

    /**
     * @deprecated unused option
     */
    public function isEnabled() {
        return true;
    }

    /**
     * Returns product's URL as configured in Magento admin.
     * @return string Product URL
     */
    public function getProductUrl($product)
    {
        if (!$product) {
            return null;
        }
        $url = Mage::getBaseUrl();

        $rewrite = Mage::getModel('core/url_rewrite');

        if ($product->getStoreId()) {
            $rewrite->setStoreId($product->getStoreId());
        } else {
            $rewrite->setStoreId(Mage::app()->getStore()->getId());
        }

        $idPath = 'product/' . $product->getId();
        if ($product->getCategoryId() && Mage::getStoreConfig('catalog/seo/product_use_categories')) {
            $idPath .= '/' . $product->getCategoryId();
        }

        $rewrite->loadByIdPath($idPath);

        if ($rewrite->getId()) {
            $url .= $rewrite->getRequestPath();
            return $url;
        }

        $url .= $product->getUrlKey() . Mage::helper('catalog/product')->getProductUrlSuffix();

        return $url;
    }

}
