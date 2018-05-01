<?php

class Phpro_Translate_Model_System_Config_Source_Store_Locales {

    public function toArray() {
        $locales = array();
        foreach (Mage::app()->getStores() as $key => $store) {
            if ($store->getConfig('general/locale/code') != 'en_US') { // We don't allow logging en_US, it's translated already.
                array_push($locales, $store->getConfig('general/locale/code'));
            }
        }
        return $locales;
    }

    public function toOptionArray() {
        $options = array();
        $usedLocales = $this->toArray();
        foreach (Mage::app()->getLocale()->getOptionLocales() as $key => $localeInfo) {
            if (in_array($localeInfo['value'], $usedLocales)) {
                array_push($options, $localeInfo);
            }
        }
        return $options;
    }

}