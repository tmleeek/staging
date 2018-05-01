<?php

class Tatva_Productproblem_Model_Mysql4_Productproblem extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the productproblem_id refers to the key field in your database table.
        $this->_init('productproblem/productproblem', 'productproblem_id');
    }
}