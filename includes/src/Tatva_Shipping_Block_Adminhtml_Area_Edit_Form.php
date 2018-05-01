<?php

/**
 * 
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Block_Adminhtml_Area_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
  /**
   * Formulaire de saisie d'une zone
   */ 	
  protected function _prepareForm() {
     
  	 $form = new Varien_Data_Form(
  	 		array('id' => 'edit_form',
                  'action' => 
  	 				$this->getUrl('*/*/save', 
		   				array('shipping_area_id' => $this->getRequest()->getParam('shipping_area_id'))),
		          'method' => 'post'
            ) );

     
      $fieldset = $form->addFieldset('shipping_area_form', 
      		array('legend'=>Mage::helper('tatvashipping')->__('Area') )
      );
      
       $fieldset->addField('area_code', 'text', array(
          'label'     => Mage::helper('tatvashipping')->__('Code'),
          'title'     => Mage::helper('tatvashipping')->__('Code'),      
          'required'  => true,
          'name'      => 'area_code',
      ));
         
      $fieldset->addField('area_label', 'text', array(
          'label'     => Mage::helper('tatvashipping')->__('Label'),
          'title'     => Mage::helper('tatvashipping')->__('Label'),      
          'required'  => true,
          'name'      => 'area_label',
      ));
      
		$fieldset->addField('countries_ids', 'multiselect', array(
                'name'      => 'countries_ids[]',
                'label'     => Mage::helper('tatvashipping')->__('Countries'),
                'title'     => Mage::helper('tatvashipping')->__('Countries'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_config_source_country')->toOptionArray(),
            ));
      $form->setValues(Mage::registry('shipping_area_data')->getData());
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}