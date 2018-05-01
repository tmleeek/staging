<?php
class Tatva_Customerattributes_Block_Adminhtml_Customeraddress extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_customeraddress';
    $this->_blockGroup = 'customerattributes';
    $this->_headerText = Mage::helper('customerattributes')->__('Manage Customer Address Attributes');
    $this->_addButtonLabel = Mage::helper('customerattributes')->__('Add New Customer Address Attribute');
    parent::__construct();
  }
}