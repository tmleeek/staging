<?php
class Tatva_Advice_Block_Adminhtml_Advice extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_advice';
    $this->_blockGroup = 'advice';
    $this->_headerText = Mage::helper('advice')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('advice')->__('Add Item');
    parent::__construct();
  }
}