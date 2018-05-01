<?php
class Tatva_Productproblem_Block_Adminhtml_Productproblem extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_productproblem';
    $this->_blockGroup = 'productproblem';
    $this->_headerText = Mage::helper('productproblem')->__('Product Problem Manager');
    $this->_addButtonLabel = Mage::helper('productproblem')->__('Add Item');
    parent::__construct();
    $this->removeButton('add');
  }
}