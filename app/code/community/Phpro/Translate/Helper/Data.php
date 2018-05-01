<?php

class Phpro_Translate_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getAdvancedLoggingStatus() {
        return Mage::getStoreConfig('translate/general/translation_logging');
    }

    public function getLocales() {
        return Mage::getStoreConfig('translate/general/locales');
    }

    public function getTranslationInterfaces() {
        $interfaces = explode(",", Mage::getStoreConfig('translate/general/translation_interfaces'));
        return $interfaces;
    }

    public function isGroupLogging() {
        $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $logArray = explode(",", Mage::getStoreConfig('translate/general/customer_groups'));
        
        if (in_array($groupId, $logArray) || in_array(-1, $logArray)) {
            return true;
        }
        
        return false;
    }
    
    public function getVersion() {
        return $version = Mage::getConfig()->getModuleConfig("Phpro_Translate")->version;
    }
    
    public function allowedToLogString($locale, $interface) {
        $allowed = false;
        
        if ($this->getAdvancedLoggingStatus()) {
            if (in_array($interface, $this->getTranslationInterfaces())) {
                    if ($this->isGroupLogging()) {
                        $locales = explode(',', $this->getLocales());
                        if (in_array($locale, $locales) && $locale != 'en_US') {
                            $allowed = true;
                        }
                    }
            }
        }
        return $allowed;
    }

}