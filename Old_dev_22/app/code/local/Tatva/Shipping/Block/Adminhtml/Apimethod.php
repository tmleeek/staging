<?php

/**
 *
 * @package Tatva_Shipping
 */
class Tatva_Shipping_Block_Adminhtml_Apimethod extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  { 
    $this->_controller = 'adminhtml_apimethod';
    $this->_blockGroup = 'tatvashipping';
    $this->_headerText = Mage::helper('tatvashipping')->__('Shipping Method For API');
    $this->_addButtonLabel = Mage::helper('tatvashipping')->__('Add Add Shipping Method For API');
    parent::__construct();
  }
  
	public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit');
    }
  
}