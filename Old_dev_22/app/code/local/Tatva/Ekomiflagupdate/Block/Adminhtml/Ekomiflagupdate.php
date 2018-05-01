<?php

class Tatva_Ekomiflagupdate_Block_Adminhtml_Ekomiflagupdate extends Mage_Adminhtml_Block_Widget_Form_Container
{
  public function __construct()
  {

    $this->_controller = 'adminhtml_ekomiflagupdate';
    $this->_blockGroup = 'ekomiflagupdate';
	$this->_headerText = Mage::helper('ekomiflagupdate')->__('Enter Order Increment Id');
	$this->_addButtonLabel = Mage::helper('ekomiflagupdate')->__('Enter Order Increment Id');
    
  

    parent::__construct();
  }

	public function getCreateUrl()
    {
        return $this->getUrl('*/*/ekomiflagupdate');
    }
  
}
