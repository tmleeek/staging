<?php

class Tatva_Material_Model_Mysql4_Material extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the material_id refers to the key field in your database table.
        $this->_init('material/material', 'material_id');
    }
}