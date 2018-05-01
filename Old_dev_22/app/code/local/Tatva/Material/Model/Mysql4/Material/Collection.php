<?php

class Tatva_Material_Model_Mysql4_Material_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('material/material');
    }
}