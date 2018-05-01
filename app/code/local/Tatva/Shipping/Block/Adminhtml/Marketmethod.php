<?php

/**
 *
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_Marketmethod extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  { 
    $this->_controller = 'adminhtml_marketmethod';
    $this->_blockGroup = 'tatvashipping';
    $this->_headerText = Mage::helper('tatvashipping')->__('Shipping Rule For MarketPlace');
    $this->_addButtonLabel = Mage::helper('tatvashipping')->__('Add Shipping Rule For MarketPlace');
    parent::__construct();
  }
  
	public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
  
}