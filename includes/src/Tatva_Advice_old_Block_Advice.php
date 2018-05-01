<?php
class Tatva_Advice_Block_Advice extends Mage_Core_Block_Template
{

	public function _prepareLayout()
    {                                   
		return parent::_prepareLayout();
    }

     public function getAdvice()
     { 
        if (!$this->hasData('advice')) {
            $this->setData('advice', Mage::registry('advice'));
        }
        return $this->getData('advice');
        
    }
   public function getadviceCollection($material)
   {
     $advice = Mage::getModel('advice/advice')->getCollection();
     $advice = $advice->addFieldToFilter('status','1');
     $advice = $advice->addFieldToFilter('material',array('like'=>'%'.$material.'%'));
     return $advice; 
   }
}