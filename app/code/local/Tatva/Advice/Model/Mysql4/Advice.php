<?php

class Tatva_Advice_Model_Mysql4_Advice extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the advice_id refers to the key field in your database table.
        $this->_init('advice/advice', 'advice_id');
    }
}