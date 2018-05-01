<?php

class Tatva_Productproblem_Block_Adminhtml_Productproblem_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('productproblem_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('productproblem')->__('Product Problem Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('productproblem')->__('General Information'),
          'title'     => Mage::helper('productproblem')->__('Problem Information'),
          'content'   => $this->getLayout()->createBlock('productproblem/adminhtml_productproblem_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}