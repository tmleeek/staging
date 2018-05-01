<?php


class MDN_Mpm_Model_Mysql4_Report_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('Mpm/Report');
    }
}