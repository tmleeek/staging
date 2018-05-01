<?php

class Tatva_Advice_Block_Adminhtml_Advice_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'advice';
        $this->_controller = 'adminhtml_advice';
        
        $this->_updateButton('save', 'label', Mage::helper('advice')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('advice')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('advice_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'advice_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'advice_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('advice_data') && Mage::registry('advice_data')->getId() ) {
            return Mage::helper('advice')->__("Edit Advice", $this->htmlEscape(Mage::registry('advice_data')->getTitle()));
        } else {
            return Mage::helper('advice')->__('Add Item');
        }
    }
}