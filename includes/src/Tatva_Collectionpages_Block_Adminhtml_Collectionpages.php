<?php
class Tatva_Collectionpages_Block_Adminhtml_Collectionpages extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_collectionpages';
    $this->_blockGroup = 'collectionpages';
    $this->_headerText = Mage::helper('collectionpages')->__('Collectionpages Manager');
    parent::__construct();
    $this->_removeButton('add');
  }
}