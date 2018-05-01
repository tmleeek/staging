<?php

class Phpro_Translate_Model_Mysql4_translate extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        // Note that the translate_id refers to the key field in your database table.
        $this->_init('translate/translate', 'translate_id');
    }

}