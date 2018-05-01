<?php

/**
 * data table : 'auto_cancel_order_log'
 * 
 */
class MDN_AutoCancelOrder_Model_Log extends Mage_Core_Model_Abstract {

    /**
     * Constructor
     *
     */
    public function _construct() {
        parent::_construct();
        $this->_init('AutoCancelOrder/Log');
    }

}