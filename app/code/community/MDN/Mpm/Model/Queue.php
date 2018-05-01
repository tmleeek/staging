<?php

class MDN_Mpm_Model_Queue extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('Mpm/Queue');
    }
}