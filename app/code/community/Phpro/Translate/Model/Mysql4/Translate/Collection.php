<?php

class Phpro_Translate_Model_Mysql4_Translate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('translate/translate');
    }

}