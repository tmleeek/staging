<?php

class Tatva_Customerattributes_Block_Adminhtml_Customerattributes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('product_attribute_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('customerattributes')->__('Customer Attribute Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('main', array(
          'label'     => Mage::helper('customerattributes')->__('Properties'),
          'title'     => Mage::helper('customerattributes')->__('Properties'),
          'content'   => $this->getLayout()->createBlock('customerattributes/adminhtml_customerattributes_edit_tab_main')->toHtml(),
      ));

      $model = Mage::registry('entity_attribute');

      $this->addTab('labels', array(
          'label'     => Mage::helper('customerattributes')->__('Manage Label / Options'),
          'title'     => Mage::helper('customerattributes')->__('Manage Label / Options'),
          'content'   => $this->getLayout()->createBlock('customerattributes/adminhtml_customerattributes_edit_tab_options')->toHtml(),
      ));
      /*$model = Mage::registry('entity_attribute');

      $this->addTab('labels', array(
            'label'     => Mage::helper('customerattributes')->__('Manage Label / Options'),
            'title'     => Mage::helper('customerattributes')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('customerattributes/adminhtml_customerattributes_edit_tab_form_options')->toHtml(),
        ));*/
      return parent::_beforeToHtml();
  }
}