<?php

class Infortis_Brands_Model_Source_Easing
{
    public function toOptionArray()
    {
        return array(
			//Ease in-out
			array('value' => 'easeInOutSine',	'label' => Mage::helper('brands')->__('easeInOutSine')),
			array('value' => 'easeInOutQuad',	'label' => Mage::helper('brands')->__('easeInOutQuad')),
			array('value' => 'easeInOutCubic',	'label' => Mage::helper('brands')->__('easeInOutCubic')),
			array('value' => 'easeInOutQuart',	'label' => Mage::helper('brands')->__('easeInOutQuart')),
			array('value' => 'easeInOutQuint',	'label' => Mage::helper('brands')->__('easeInOutQuint')),
			array('value' => 'easeInOutExpo',	'label' => Mage::helper('brands')->__('easeInOutExpo')),
			array('value' => 'easeInOutCirc',	'label' => Mage::helper('brands')->__('easeInOutCirc')),
			array('value' => 'easeInOutElastic','label' => Mage::helper('brands')->__('easeInOutElastic')),
			array('value' => 'easeInOutBack',	'label' => Mage::helper('brands')->__('easeInOutBack')),
			array('value' => 'easeInOutBounce',	'label' => Mage::helper('brands')->__('easeInOutBounce')),
			//Ease out
			array('value' => 'easeOutSine',		'label' => Mage::helper('brands')->__('easeOutSine')),
			array('value' => 'easeOutQuad',		'label' => Mage::helper('brands')->__('easeOutQuad')),
			array('value' => 'easeOutCubic',	'label' => Mage::helper('brands')->__('easeOutCubic')),
			array('value' => 'easeOutQuart',	'label' => Mage::helper('brands')->__('easeOutQuart')),
			array('value' => 'easeOutQuint',	'label' => Mage::helper('brands')->__('easeOutQuint')),
			array('value' => 'easeOutExpo',		'label' => Mage::helper('brands')->__('easeOutExpo')),
			array('value' => 'easeOutCirc',		'label' => Mage::helper('brands')->__('easeOutCirc')),
			array('value' => 'easeOutElastic',	'label' => Mage::helper('brands')->__('easeOutElastic')),
			array('value' => 'easeOutBack',		'label' => Mage::helper('brands')->__('easeOutBack')),
			array('value' => 'easeOutBounce',	'label' => Mage::helper('brands')->__('easeOutBounce')),
			//Ease in
			array('value' => 'easeInSine',		'label' => Mage::helper('brands')->__('easeInSine')),
			array('value' => 'easeInQuad',		'label' => Mage::helper('brands')->__('easeInQuad')),
			array('value' => 'easeInCubic',		'label' => Mage::helper('brands')->__('easeInCubic')),
			array('value' => 'easeInQuart',		'label' => Mage::helper('brands')->__('easeInQuart')),
			array('value' => 'easeInQuint',		'label' => Mage::helper('brands')->__('easeInQuint')),
			array('value' => 'easeInExpo',		'label' => Mage::helper('brands')->__('easeInExpo')),
			array('value' => 'easeInCirc',		'label' => Mage::helper('brands')->__('easeInCirc')),
			array('value' => 'easeInElastic',	'label' => Mage::helper('brands')->__('easeInElastic')),
			array('value' => 'easeInBack',		'label' => Mage::helper('brands')->__('easeInBack')),
			array('value' => 'easeInBounce',	'label' => Mage::helper('brands')->__('easeInBounce')),
			//No easing
			array('value' => '',				'label' => Mage::helper('brands')->__('Disabled'))
        );
    }
}
