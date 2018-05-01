<?php

class Tatva_Productproblem_Model_Productproblem extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('productproblem/productproblem');
    }
}