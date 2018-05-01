<?php
class Tatva_Material_Block_Material extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getMaterial()     
     { 
        if (!$this->hasData('material')) {
            $this->setData('material', Mage::registry('material'));
        }
        return $this->getData('material');
        
    }
}