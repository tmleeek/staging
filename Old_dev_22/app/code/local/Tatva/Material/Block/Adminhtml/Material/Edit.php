<?php

class Tatva_Material_Block_Adminhtml_Material_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'material';
        $this->_controller = 'adminhtml_material';
        
        $this->_updateButton('save', 'label', Mage::helper('material')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('material')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('material_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'material_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'material_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('material_data') && Mage::registry('material_data')->getId() ) {
            return Mage::helper('material')->__("Edit Material ", $this->htmlEscape(Mage::registry('material_data')->getTitle()));
        } else {
            return Mage::helper('material')->__('Add Item');
        }
    }
}