<?php

class Tatva_Marqueproducts_Model_Mysql4_Marqueproducts_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('marqueproducts/marqueproducts');
    }
}