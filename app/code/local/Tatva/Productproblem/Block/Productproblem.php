<?php
class Tatva_Productproblem_Block_Productproblem extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getProductproblem()     
     { 
        if (!$this->hasData('productproblem')) {
            $this->setData('productproblem', Mage::registry('productproblem'));
        }
        return $this->getData('productproblem');
        
    }

    public function getContent()
    {
      Mage::getSingleton('core/session')->addSuccess('Your problem has been sent');
      return $this;
    }
}