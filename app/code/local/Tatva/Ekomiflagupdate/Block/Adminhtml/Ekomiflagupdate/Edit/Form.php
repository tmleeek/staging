<?php

class Tatva_Ekomiflagupdate_Block_Adminhtml_Ekomiflagupdate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
	
  /**
   * Formulaire de saisie d'une zone
   * 
   * EXIG FOU-001 FOU-002
   * REG BO-600
   */
  protected function _prepareForm() 
  {
  	  $form = new Varien_Data_Form(
  	 		array('name' => 'edit_form',
                   'id'=>'edit_form',
                  'action' =>
  	 				$this->getUrl('*/*/save',
		   				array('order_increment_id' => $this->getRequest()->getParam('order_increment_id'))),
		          'method' => 'post'
            ));

      $fieldset = $form->addFieldset('edit_form',
      		array('legend'=>Mage::helper('ekomiflagupdate')->__('Ekomiflag Update') )
      );
      
      $fieldset->addField('order_increment_id', 'text', array(
          'label'     => Mage::helper('ekomiflagupdate')->__('Order Increment Id'),
          'title'     => Mage::helper('ekomiflagupdate')->__('Order Increment Id'),
          'required'  => true,
          'name'      => 'order_increment_id',
		  'id'      =>   'order_increment_id',
          

      ));

	  $form->setUseContainer(true);
	  $this->setForm($form);
      return parent::_prepareForm();
  }
}