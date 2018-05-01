<?php

/**
 *
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_Area extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  { 
    $this->_controller = 'adminhtml_area';
    $this->_blockGroup = 'tatvashipping';
    $this->_headerText = Mage::helper('tatvashipping')->__('Areas'); 
    $this->_addButtonLabel = Mage::helper('tatvashipping')->__('Add New Area');
    parent::__construct();
  }
  
	public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
  
}