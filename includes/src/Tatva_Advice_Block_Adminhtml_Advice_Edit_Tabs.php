<?php

class Tatva_Advice_Block_Adminhtml_Advice_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('advice_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('advice')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('advice')->__('Item Information'),
          'title'     => Mage::helper('advice')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('advice/adminhtml_advice_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}