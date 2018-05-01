<?php

class MDN_AutoCancelOrder_Model_Mysql4_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('AutoCancelOrder/Log');
    }

}