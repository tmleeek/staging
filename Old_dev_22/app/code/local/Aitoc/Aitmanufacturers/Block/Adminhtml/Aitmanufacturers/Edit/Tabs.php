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

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('aitmanufacturers_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('aitmanufacturers')->__(Mage::getModel('aitmanufacturers/config')->getAttributeName($this->getRequest()->get('attributecode')).' Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('aitmanufacturers')->__('General Information'),
          'title'     => Mage::helper('aitmanufacturers')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit_tab_form')->toHtml(),
      ));
      /*$this->addTab('display', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Display Settings'),
          'title'     => Mage::helper('aitmanufacturers')->__('Display Settings'),
          'content'   => $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit_tab_display')->toHtml(),
      ));*/
      $this->addTab('design', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Custom Design'),
          'title'     => Mage::helper('aitmanufacturers')->__('Custom Design'),
          'content'   => $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit_tab_design')->toHtml(),
      ));
  
      $this->addTab('associated_products', array(
          'label'     => Mage::helper('aitmanufacturers')->__('Associated products'),
          'title'     => Mage::helper('aitmanufacturers')->__('Associated products'),
          'content'   => $this->getLayout()->createBlock('aitmanufacturers/adminhtml_aitmanufacturers_edit_tab_product')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}