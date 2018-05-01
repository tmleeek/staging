<?php

class Phpro_Translate_Model_System_Config_Source_Stores {

    public function toArray() {
        $stores = array();
        $stores[0] = "Main Website";
        foreach (Mage::app()->getStores() as $key => $store) {
            $stores[$store->getId()] = $store->getName();
        }
        return $stores;
    }

}