<?php

/**
 *
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Block_Adminhtml_Apimethod_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	$this->_objectId = 'shipping_apimethod_id';
        $this->_blockGroup = 'tatvashipping';
        $this->_controller = 'adminhtml_apimethod';
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('tatvashipping')->__('Save Shipping Method For API'));
        $this->_updateButton('delete', 'label', Mage::helper('tatvashipping')->__('Delete Shipping Method For API'));
 
    }

    public function getHeaderText()
    {
        if( Mage::registry('shipping_apimethod_id') && Mage::registry('shipping_apimethod_id')->getId() ) {
            return Mage::helper('tatvashipping')->__("Edit Shipping Method For API", $this->htmlEscape(Mage::registry('shipping_apimethod_data')->getId()));
        } else {
            return Mage::helper('tatvashipping')->__('Add Shipping Method For API');
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