<?php

/**
 * 
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Block_Adminhtml_Apimethod_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	
  /**
   * Formulaire de saisie d'une zone
   */ 	
  protected function _prepareForm() {
     
  	 $form = new Varien_Data_Form(
  	 		array('id' => 'edit_form',
                  'action' =>
  	 				$this->getUrl('*/*/save',
		   				array('shipping_apimethod_id' => $this->getRequest()->getParam('shipping_apimethod_id'))),
                  'enctype' => 'multipart/form-data',
		          'method' => 'post'
            ) );

        $shipping_api_id = $this->getRequest()->getParam('shipping_apimethod_id');
        $logo = Mage::getModel('tatvashipping/apimethod')->load($shipping_api_id);

        $after_html = '';

        if( $logo->getFilename() )
        {
            $path = Mage::getBaseUrl('media')."shippingicons/original/".$logo->getFilename();
            $after_html = '<a onclick="imagePreview(slider); return false;" href="'.$path.'">
                  <img height="22" width="22" class="small-image-preview v-middle" alt="'.$logo->getFilename().'" title="'.$logo->getFilename().'" id="slider" src="'.$path.'"/>
                  </a>';
        }

        $fieldset = $form->addFieldset('shipping_apimethod_form',
      		array('legend'=>Mage::helper('tatvashipping')->__('Area') )
        );

       $fieldset->addField('shipping_method_name', 'text', array(
          'label'     => Mage::helper('tatvashipping')->__('Shipping Method Name'),
          'title'     => Mage::helper('tatvashipping')->__('Shipping Method Name'),
          'required'  => true,
          'name'      => 'shipping_method_name',
      ));

      $fieldset->addField('shipping_method_code', 'text', array(
          'label'     => Mage::helper('tatvashipping')->__('Shipping Method Code'),
          'title'     => Mage::helper('tatvashipping')->__('Shipping Method Code'),
          'required'  => true,
          'name'      => 'shipping_method_code',
      ));

      $fieldset->addField('api_shipping_code', 'text', array(
          'label'     => Mage::helper('tatvashipping')->__('API Method'),
          'title'     => Mage::helper('tatvashipping')->__('API Method'),
          'required'  => true,
          'name'      => 'api_shipping_code',
      ));

       $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('tatvashipping')->__('Logo'),
          'name'      => 'filename',
          'after_element_html' => $after_html,
          'class'     => (($logo->getfilename()) ? '' : 'required-entry'),
          'required'  => (($logo->getfilename()) ? false : true),
          'note'      => Mage::helper('tatvashipping')->__('Upload upto 1 MB'),
	  ));
      

      $form->setValues(Mage::registry('shipping_apimethod_data')->getData());
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}