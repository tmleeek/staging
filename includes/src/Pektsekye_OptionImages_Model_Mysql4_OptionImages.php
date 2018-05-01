<?php

class Pektsekye_OptionImages_Model_Mysql4_OptionImages extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the rim_id refers to the key field in your database table.
        $this->_init('optionimages/product_option_type_image', 'option_type_image_id');
    }
}