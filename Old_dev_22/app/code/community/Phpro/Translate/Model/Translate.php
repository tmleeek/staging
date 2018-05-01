<?php

class Phpro_Translate_Model_Translate extends Mage_Core_Model_Abstract {

    private $_storeName = null;

    public function _construct() {
        parent::_construct();
        $this->_init('translate/translate');
    }

    public function getStoreName() {
        if (is_null($this->_storeName)) {
            $this->_storeName = Mage::app()->getStore($this->getStoreId())
                    ->getName();
        }
        return $this->_storeName;
    }

}