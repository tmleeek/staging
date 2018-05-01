<?php

class Tatva_Advice_Model_Advice extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advice/advice');
    }
}