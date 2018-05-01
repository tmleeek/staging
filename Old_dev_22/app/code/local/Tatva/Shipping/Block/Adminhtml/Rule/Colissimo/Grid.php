<?php

/**
 * 
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_Rule_Colissimo_Grid extends Tatva_Shipping_Block_Adminhtml_Rule_Abstract {
	
  protected function _prepareCollection(){
      $collection = Mage::getModel('tatvashipping/rule')->getCollection()
      	->addShippingFilter(Tatva_Shipping_Model_Rule::COLISSIMO);

      $this->setCollection($collection);
      parent::_prepareCollection();
      
      $this->getCollection()->addAreasToResult();
      return $this;
  }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', 
        	array(
        		'shipping_rule_id' => $row->getId(),
        		'shipping_code' => Tatva_Shipping_Model_Rule::COLISSIMO
        	));
    }
}