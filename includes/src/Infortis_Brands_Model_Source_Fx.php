<?php

class Infortis_Brands_Model_Source_Fx
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'slide',	'label' => Mage::helper('brands')->__('Slide')),
			array('value' => 'fade',	'label' => Mage::helper('brands')->__('Fade'))
        );
    }
}
