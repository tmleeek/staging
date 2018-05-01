<?php

class MDN_Mpm_Model_Mysql4_Rule_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('Mpm/Rule_Product', 'id');
    }
}
