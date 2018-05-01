<?php
class Tatva_Productproblem_Block_Adminhtml_Mailform extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $return = parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $return;
    }

    protected function _prepareForm()
    {
      $data = $this->getRequest()->getParams();
      $model = Mage::getModel('productproblem/productproblem')->getCollection()->addFieldToFilter("productproblem_id",$data['problemid'])->getData();
      $form = new Varien_Data_Form(array(
                                      'id' => 'mailform',
                                      'action' => $this->getUrl('*/*/sendemail', array('problemid' => $this->getRequest()->getParam('problemid'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   ));

      $form->setHtmlIdPrefix('productproblem');
      $fieldset = $form->addFieldset('productproblem_form', array('legend'=>Mage::helper('productproblem')->__('Product Problem Information')));

      $fieldset->addField('productproblem_id', 'label', array(
          'label'     => Mage::helper('productproblem')->__('Problem ID:'),
          'readonly' => true,
          'name'      => 'productproblem_id',
          'value'   =>$model[0]['productproblem_id'],
      ));

      $fieldset->addField('name', 'label', array(
          'label'     => Mage::helper('productproblem')->__('Name:'),
          'readonly' => true,
          'name'      => 'name',
          'value'   =>$model[0]['name'],
      ));

      $fieldset->addField('bibno', 'label', array(
          'label'     => Mage::helper('productproblem')->__('BIB Number:'),
          'readonly' => true,
          'name'      => 'bibno',
          'value'   =>$model[0]['bibno'],
      ));

      $fieldset->addField('email', 'label', array(
          'label'     => Mage::helper('productproblem')->__('Email:'),
          'readonly' => true,
          'name'      => 'email',
          'value'   =>$model[0]['email'],
      ));

      $fieldset->addField('subject', 'text', array(
          'label'     => Mage::helper('productproblem')->__('Subject:'),
          'name'      => 'subject',
      ));


      $fieldset->addField('content', 'editor', array(
          'name' => 'content',
          'label' => Mage::helper('productproblem')->__('Content'),
          'title' => Mage::helper('productproblem')->__('Content'),
          'style' => 'width:700px; height:300px;',
          'wysiwyg' => true,
          'required' => true,
          'state' => 'html',
          'config' => $wysiwygConfig,
          ));

      $fieldset->addField('submit', 'submit', array(
          'name'      => 'submit',
          'value'   =>'Submit',
          //'onsubmit'=>'productproblem/adminhtml_productproblem/sendemail',

      ));


      if ( Mage::getSingleton('adminhtml/session')->getProductproblemData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getProductproblemData());
          Mage::getSingleton('adminhtml/session')->setProductproblemData(null);
      } elseif ( Mage::registry('productproblem_data') ) {
          $form->setValues(Mage::registry('productproblem_data')->getData());
      }
      $form->setUseContainer(true);
      $this->setForm($form);
      return $this;
  }
}