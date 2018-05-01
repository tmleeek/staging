<?php

class MDN_Mpm_Model_Mysql4_Report extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('Mpm/Report', 'id');
    }
}
