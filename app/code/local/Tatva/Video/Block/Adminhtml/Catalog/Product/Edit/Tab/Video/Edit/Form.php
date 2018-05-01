<?php
/**
 * created : 17 septembre 2009
 * 
 * EXIG FOU-001 FOU-002
 * REG BO-601
 * 
 * @category SQLI
 * @package Sqli_Video
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

/**
 * 
 * @package Sqli_Video
 */
class Tatva_Video_Block_Adminhtml_Catalog_Product_Edit_Tab_Video_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
	
  /**
   * Formulaire de saisie d'une zone
   * 
   * EXIG FOU-001, FOU-002, P-006, P-007
   * REG BO-104, BO-601
   */ 	
  protected function _prepareForm() {
        
  	 $form = new Varien_Data_Form(
  	 		array('id' => 'edit_form',
                  'action' => $this->getUrl('tatvavideo/adminhtml_video/save',
  	 					array(
  	 						'popup' => true,
  	 						'video_item_id' => $this->getRequest()->getParam('video_item_id'),
  	 					)),
		          'method' => 'post'
            ) );
        
      $fieldset = $form->addFieldset('video_form', 
      		array('legend'=>Mage::helper('tatvavideo')->__('Video') )
      );
      
      $fieldset->addField('product_id', 'hidden',
            array(
                'name'  => 'product_id',
            )
        );
        
      if (Mage::registry('video_item_data')->getId()) {
		 $fieldset->addField('video_item_id', 'hidden',
	            array(
	                'name'  => 'video_item_id',
	            )
	        );
      }
      
     
               
      $fieldset->addField('video_url', 'textarea', array(
          'label'     => Mage::helper('tatvavideo')->__('Video Url'),
          'title'     => Mage::helper('tatvavideo')->__('Video Url'),
          'required'  => true,
          'name'      => 'video_url'
      ));
      
	
	      
      $form->setValues(Mage::registry('video_item_data')->getData());
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
  
  
}
