<?php

/**
 *
 * @package Tatva_Shipping
 */

class Tatva_Shipping_Block_Adminhtml_Area_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
    	$this->_objectId = 'shipping_area_id';
        $this->_blockGroup = 'tatvashipping';
        $this->_controller = 'adminhtml_area';    	 
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('tatvashipping')->__('Save Area'));
        $this->_updateButton('delete', 'label', Mage::helper('tatvashipping')->__('Delete Area'));
 
    }

    public function getHeaderText()
    {
        if( Mage::registry('shipping_area_data') && Mage::registry('shipping_area_data')->getId() ) {
            return Mage::helper('tatvashipping')->__("Edit Area", $this->htmlEscape(Mage::registry('shipping_area_data')->getId()));
        } else {
            return Mage::helper('tatvashipping')->__('Add Area');
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