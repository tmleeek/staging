<?php
class Tatva_Customerattributes_Block_Adminhtml_Customerattributes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_customerattributes';
    $this->_blockGroup = 'customerattributes';
    $this->_headerText = Mage::helper('customerattributes')->__('Manage Customer Attributes');
    $this->_addButtonLabel = Mage::helper('customerattributes')->__('Add New Customer Attribute');
    parent::__construct();
  }
}