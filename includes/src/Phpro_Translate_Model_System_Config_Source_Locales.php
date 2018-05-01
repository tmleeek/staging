<?php

class Phpro_Translate_Model_System_Config_Source_Locales {

    public function toArray() {
        $locales = array();
        $options = array();
        $collection = Mage::getModel('translate/translate')->getCollection();
        $collection->getSelect()->group('locale');
        
        foreach (Mage::app()->getStores() as $store) {
            $locale = Mage::app()->getStore($store->getId())->getConfig('general/locale/code');
            array_push($locales, $locale);
        }
        
        foreach (Mage::app()->getLocale()->getOptionLocales() as $key => $localeInfo) {
            if (in_array($localeInfo['value'], $locales)) {
                $options[$localeInfo['value']] = $localeInfo['value'].' ['.$localeInfo['label'].']';
            }
        }

        return $options;
    }

}