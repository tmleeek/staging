<?php

class Tatva_Freegift_Model_Freegift extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('freegift/freegift');
    }
}