<?php

class MDN_AutoCancelOrder_Model_Mysql4_Log extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('AutoCancelOrder/Log', 'aco_id');
    }

}

?>