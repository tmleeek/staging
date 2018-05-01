<?php

/**
 *
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Block_Adminhtml_Marketmethod_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	$this->_objectId = 'shipping_marketmethod_id';
        $this->_blockGroup = 'tatvashipping';
        $this->_controller = 'adminhtml_marketmethod';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('tatvashipping')->__('Save Shipping Rule For MarketPlace'));
        $this->_updateButton('delete', 'label', Mage::helper('tatvashipping')->__('Delete Shipping Rule For MarketPlace'));
 
    }

    public function getHeaderText()
    {
        if( Mage::registry('shipping_marketmethod_id') && Mage::registry('shipping_marketmethod_id')->getId() ) {
            return Mage::helper('tatvashipping')->__("Edit Shipping Rule For MarketPlace", $this->htmlEscape(Mage::registry('shipping_marketmethod_data')->getId()));
        } else {
            return Mage::helper('tatvashipping')->__('Add Shipping Rule For MarketPlace');
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