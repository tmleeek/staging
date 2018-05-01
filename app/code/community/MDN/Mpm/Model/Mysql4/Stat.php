<?php

class MDN_Mpm_Model_Mysql4_Stat extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('Mpm/Stat', 'id');
    }
}
