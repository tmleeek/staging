<?php
       
/**
 * 
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Block_Adminhtml_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	$this->_objectId = 'shipping_rule_id';
        $this->_blockGroup = 'tatvashipping';
        $this->_controller = 'adminhtml_rule';    	 
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('tatvashipping')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('tatvashipping')->__('Delete Rule'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('shipping_rule_data') && Mage::registry('shipping_rule_data')->getId() ) {
            return Mage::helper('tatvashipping')->__("Edit Rule", $this->htmlEscape(Mage::registry('shipping_rule_data')->getId()));
        } else {
            return Mage::helper('tatvashipping')->__('Add Rule');
        }
    }
    
    /**
     * URL Validate action
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
}