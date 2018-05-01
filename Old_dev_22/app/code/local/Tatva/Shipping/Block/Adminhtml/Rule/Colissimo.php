<?php


/**
 * 
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Block_Adminhtml_Rule_Colissimo extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_rule_colissimo';
    $this->_blockGroup = 'tatvashipping';
    $this->_headerText = Mage::helper('tatvashipping')->__('Colissimo : Grid price / weight');
    $this->_addButtonLabel = Mage::helper('tatvashipping')->__('Add New Rule');
    parent::__construct();
  }
  
	public function getCreateUrl()
    {
        return $this->getUrl('*/*/edit',
        	array(
        		'shipping_code' => Tatva_Shipping_Model_Rule::COLISSIMO
        	));
    }
}