<?php


/**
 * 
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Block_Adminhtml_Rule_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
	
  /**
   * Formulaire de saisie d'une règle
   * EXIG TRA-001
   * REG BO-712
   */ 	
  protected function _prepareForm() {
     $shippingcode = $this->getRequest()->getParam('shipping_code');
     
  	 $form = new Varien_Data_Form(
  	 		array('id' => 'edit_form',
                  'action' => 
  	 				$this->getUrl('*/*/save', 
		   				array('shipping_rule_id' => $this->getRequest()->getParam('shipping_rule_id'),
		   					  'shipping_code' => $shippingcode
		   				)),
		          'method' => 'post'
            ) );

     
      $fieldset = $form->addFieldset('shipping_rule_form', 
      		array('legend'=>Mage::helper('tatvashipping')->__('Grid price / weight') . ' : ' . $shippingcode)
      );
    
      $fieldset->addField('weight_min', 'text', array(
          'label'     => Mage::helper('tatvashipping')->__('Minimum weight'),
          'title'     => Mage::helper('tatvashipping')->__('Minimum weight'),      
          'class'     => 'validate-number',
          'required'  => true,
          'name'      => 'weight_min',
      ));
      $fieldset->addField('weight_max', 'text', array(
          'label'     => Mage::helper('tatvashipping')->__('Maximum weight'),
      	  'title'     => Mage::helper('tatvashipping')->__('Maximum weight'),
      	  'class'     => 'validate-number',
          'name'      => 'weight_max',
      	  'required'  => true,
      ));		
    	
	  $fieldset->addField('amount', 'text', array(
          'label'     => Mage::helper('tatvashipping')->__('Amount'),
	  	  'title'     => Mage::helper('tatvashipping')->__('Amount'),
      	  'class'     => 'validate-number',
          'name'      => 'amount',
      	  'required'  => true,
      ));	
      
		$fieldset->addField('areas_ids', 'multiselect', array(
                'name'      => 'areas_ids[]',
                'label'     => Mage::helper('tatvashipping')->__('Areas'),
                'title'     => Mage::helper('tatvashipping')->__('Areas'),
                'required'  => true,
                'values'    => $this->getAreas(),
            ));
      
      $form->setValues(Mage::registry('shipping_rule_data')->getData());
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
  
  private function getAreas(){
  	$options = array();
  	$collection = Mage::getModel('tatvashipping/area')->getCollection();
  	foreach($collection as $area){
  		$options[] = array('value'=>$area->getId(),'label'=>$area->getAreaLabel());
  	}
  	return $options;
  }
}