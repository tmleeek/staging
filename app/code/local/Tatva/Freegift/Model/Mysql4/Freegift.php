<?php

class Tatva_Freegift_Model_Mysql4_Freegift extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the freegift_id refers to the key field in your database table.
        $this->_init('freegift/freegift', 'freegift_id');
    }
}