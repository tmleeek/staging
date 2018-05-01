<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers_Edit_Tab_Display extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('aitmanufacturers_display', array('legend'=>Mage::helper('aitmanufacturers')->__('Display Settings')));
     
      $fieldset->addField('available_sort_by', 'multiselect', array(
                'name'      => 'available_sort_by[]',
                'label'     => Mage::helper('aitmanufacturers')->__('Available Product Listing Sort By'),
                'title'     => Mage::helper('aitmanufacturers')->__('Available Product Listing Sort By'),
                'required'  => true,
                'values'    => array(),
            ));

      if ( Mage::getSingleton('adminhtml/session')->getAitmanufacturersData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAitmanufacturersData());
          Mage::getSingleton('adminhtml/session')->setAitmanufacturersData(null);
      } elseif ( Mage::registry('aitmanufacturers_data') ) {
          $form->setValues(Mage::registry('aitmanufacturers_data')->getData());
      }
      return parent::_prepareForm();
  }
}