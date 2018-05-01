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
   public function getadviceCollection($material,$store_id)
   {
     $advice_text_collection=array();

      foreach($material[0] as $material_data)
      {
        $advice='';
        $advice = Mage::getModel('advice/advice')->getCollection();
        $advice = $advice->addFieldToFilter('status','1');
        $advice = $advice->addFieldToFilter('store_id', array('like'=>'%'.$store_id.'%'));
        $advice = $advice->addFieldToFilter('material',array('like'=>'%'.$material_data.'%'));

        foreach($advice as $advices)
        {
         $advice_text_collection[]=  $advices['advice_text'];
        }
      }
      $advice_text_collection=array_unique($advice_text_collection);

     return $advice_text_collection;
   }
}