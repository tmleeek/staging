<?php

class Tatva_Material_Block_Adminhtml_Material_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('material_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('material')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('material')->__('Item Information'),
          'title'     => Mage::helper('material')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('material/adminhtml_material_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}