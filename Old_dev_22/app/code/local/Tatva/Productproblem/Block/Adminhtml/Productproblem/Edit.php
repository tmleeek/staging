<?php

class Tatva_Productproblem_Block_Adminhtml_Productproblem_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'productproblem';
        $this->_controller = 'adminhtml_productproblem';
        
        $this->_updateButton('save', 'label', Mage::helper('productproblem')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('productproblem')->__('Delete Item'));
		$this->removeButton('save');
        $id = Mage::registry('productproblem_data')->getId();

        $url = $this->getUrl('productproblem/adminhtml_productproblem/popup').'problemid/'.$id.'/';
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productproblem_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productproblem_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productproblem_content');
                }
            }
           function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }

            function openMyPopup() {
                 window.open('".$url."','_self');

            }
            function closePopup() {
                Windows.close('browser_window');
            }

            ";

        $this->_addButton('reply', array(
        'label' => Mage::helper('adminhtml')->__('Reply'),
        'class' => 'form-button',
        'onclick' => 'javascript:openMyPopup()'

        ),-1,4,'footer');


    }

    public function getHeaderText()
    {
        if( Mage::registry('productproblem_data') && Mage::registry('productproblem_data')->getId() ) {
            return Mage::helper('productproblem')->__("Reply to Problem of %s (%s)", $this->htmlEscape(Mage::registry('productproblem_data')->getName()),$this->htmlEscape(Mage::registry('productproblem_data')->getEmail()));
        } else {
            return Mage::helper('productproblem')->__('Add Item');
        }
    }
}