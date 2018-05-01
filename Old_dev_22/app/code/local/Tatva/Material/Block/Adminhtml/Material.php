<?php
class Tatva_Material_Block_Adminhtml_Material extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_material';
    $this->_blockGroup = 'material';
    $this->_headerText = Mage::helper('material')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('material')->__('Add Item');
    parent::__construct();
  }
}