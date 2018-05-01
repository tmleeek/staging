<?php

class Pektsekye_OptionImages_Model_OptionImages extends Mage_Core_Model_Abstract
{	
    public function _construct()
    {
        parent::_construct();
        $this->_init('OptionImages/OptionImages');
    }
}