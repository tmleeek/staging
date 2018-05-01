<?php

/**
 *
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_Cartshipping extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  { 
    $this->_controller = 'adminhtml_cartshipping';
    $this->_blockGroup = 'tatvashipping';
    $this->_headerText = Mage::helper('tatvashipping')->__('Default Shipping Method For Cart');
    $this->_addButtonLabel = Mage::helper('tatvashipping')->__('Add Default Shipping Method For Cart');
    parent::__construct();
  }
  
	public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
  
}