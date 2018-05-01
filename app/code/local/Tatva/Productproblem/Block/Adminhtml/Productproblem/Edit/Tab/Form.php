<?php

class Tatva_Productproblem_Block_Adminhtml_Productproblem_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
     // $sku = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('entity_id', array('in' =>'productid'))->getData();
     // var_dump($sku);
      $fieldset = $form->addFieldset('productproblem_form', array('legend'=>Mage::helper('productproblem')->__('Product Problem Information')));
     
      $fieldset->addField('productproblem_id', 'label', array(
          'label'     => Mage::helper('productproblem')->__('Problem ID:'),
          'readonly' => true,
          'name'      => 'productproblem_id',
      ));

      $fieldset->addField('productid', 'label', array(
          'label'     => Mage::helper('productproblem')->__('Product ID:'),
          'readonly' => true,
          'name'      => 'productid',
      ));

      $fieldset->addField('sku', 'label', array(
          'label'     => Mage::helper('productproblem')->__('Product SKU:'),
          'readonly' => true,
          'name'      => 'sku',
        //  'value'=>$sku,
      ));

      $fieldset->addField('name', 'label', array(
          'label'     => Mage::helper('productproblem')->__('Name:'),
          'readonly' => true,
          'name'      => 'name',
      ));

      $fieldset->addField('email', 'label', array(
          'label'     => Mage::helper('productproblem')->__('Email:'),
          'readonly' => true,
          'name'      => 'email',
      ));


      $fieldset->addField('comment', 'label', array(
          'name'      => 'comment',
          'label'     => Mage::helper('productproblem')->__('Comment:'),
          'title'     => Mage::helper('productproblem')->__('Comment:'),
          'readonly' => true,
      ));

      if ( Mage::getSingleton('adminhtml/session')->getProductproblemData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getProductproblemData());
          Mage::getSingleton('adminhtml/session')->setProductproblemData(null);
      } elseif ( Mage::registry('productproblem_data') ) {
            $arr = Mage::registry('productproblem_data')->getData();
            $product_sku = Mage::getModel('catalog/product')->load($arr['productid'])->getSku();
            $arr['sku'] = $product_sku;
            $form->setValues($arr);
      }
      return parent::_prepareForm();
  }
}