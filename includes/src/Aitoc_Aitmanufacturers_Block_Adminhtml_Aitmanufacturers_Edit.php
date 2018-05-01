<?php
/**
 * Shop By Brands
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitmanufacturers
 * @version      3.3.1
 * @license:     zAuKpf4IoBvEYeo5ue8Cll0eto0di8JUzOnOWiuiAF
 * @copyright:   Copyright (c) 2014 AITOC, Inc. (http://www.aitoc.com)
 */
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/

class Aitoc_Aitmanufacturers_Block_Adminhtml_Aitmanufacturers_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'aitmanufacturers';
        $this->_controller = 'adminhtml_aitmanufacturers';
        
        $this->_updateButton('save', 'label', Mage::helper('aitmanufacturers')->__('Save Attribute Page'));
        $this->_updateButton('delete', 'label', Mage::helper('aitmanufacturers')->__('Delete Attribute Page'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('content_editor') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content_editor');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content_editor');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('aitmanufacturers_data') && Mage::registry('aitmanufacturers_data')->getId() ) {
            return Mage::helper('aitmanufacturers')->__("Edit Attribute Page '%s'", $this->htmlEscape(Mage::registry('aitmanufacturers_data')->getManufacturer()));
        } else {
            return Mage::helper('aitmanufacturers')->__('Add Attribute Page');
        }
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/', array(
            'store' =>  $this->getRequest()->get('store'),
            'attributecode' => $this->getRequest()->get('attributecode')
        ));
    }
    
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId), 'attributecode' => $this->getRequest()->get('attributecode')));
    }

    public function getFormActionUrl()
    {
        
        return $this->getUrl('*/' . $this->_controller . '/save', array('attributecode' => $this->getRequest()->get('attributecode')));
    }
}