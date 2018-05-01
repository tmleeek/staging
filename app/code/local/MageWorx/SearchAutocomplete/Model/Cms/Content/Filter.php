<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Search Autocomplete extension
 *
 * @category   MageWorx
 * @package    MageWorx_SearchAutocomplete
 * @author     MageWorx Dev Team
 */

class MageWorx_SearchAutocomplete_Model_Cms_Content_Filter extends Mage_Core_Model_Email_Template_Filter {

    protected $_designSettings;
    
    protected function _applyDesignSettings() {
        if ($this->getDesignSettings()) {
            $design = Mage::getDesign();
            $this->getDesignSettings()
                    ->setOldArea($design->getArea())
                    ->setOldStore($design->getStore());

            if ($this->getDesignSettings()->getArea()) {
                Mage::getDesign()->setArea($this->getDesignSettings()->getArea());
            }

            if ($this->getDesignSettings()->getStore()) {
                Mage::app()->getLocale()->emulate($this->getDesignSettings()->getStore());
                $design->setStore($this->getDesignSettings()->getStore());
                $design->setPackageName('');
                $design->setTheme('');
            }
        }
        return $this;
    }

    public function setDesignSettings(array $settings) {
        $this->getDesignSettings()->setData($settings);
        return $this;
    }

    protected function _resetDesignSettings() {
        if ($this->getDesignSettings()) {
            if ($this->getDesignSettings()->getOldArea()) {
                Mage::getDesign()->setArea($this->getDesignSettings()->getOldArea());
            }

            if ($this->getDesignSettings()->getOldStore()) {
                Mage::getDesign()->setStore($this->getDesignSettings()->getOldStore());
                Mage::getDesign()->setPackageName('');
                Mage::getDesign()->setTheme('');
            }
        }
        Mage::app()->getLocale()->revert();
        return $this;
    }
    
    public function getDesignSettings() {
        if (is_null($this->_designSettings)) {
            $this->_designSettings = new Varien_Object();
        }
        return $this->_designSettings;
    }
    
    public function process($content) {
        $this->_applyDesignSettings();
        try {
            $result = $this->filter($content);
        } catch (Exception $e) {
            $this->_resetDesignSettings();
            throw $e;
        }
        $this->_resetDesignSettings();
        return $result;
    }

}