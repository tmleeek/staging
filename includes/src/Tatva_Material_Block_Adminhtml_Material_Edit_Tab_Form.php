<?php

class Tatva_Material_Block_Adminhtml_Material_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('material_form', array('legend'=>Mage::helper('material')->__('Item information')));
     
      $fieldset->addField('material', 'textarea', array(
          'label'     => Mage::helper('material')->__('Material'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'material',
          'after_element_html' => '<P><b>'.Mage::helper('material')->__('Please Enter Comma Separated Values For Materials.').'</b></P>',
      ));

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('material')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('material')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('material')->__('Disabled'),
              ),
          ),
      ));

      if ( Mage::getSingleton('adminhtml/session')->getMaterialData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getMaterialData());
          Mage::getSingleton('adminhtml/session')->setMaterialData(null);
      } elseif ( Mage::registry('material_data') ) {
          $form->setValues(Mage::registry('material_data')->getData());
      }
      return parent::_prepareForm();
  }
}