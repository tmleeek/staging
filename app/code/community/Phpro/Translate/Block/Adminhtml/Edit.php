<?php

class Phpro_Translate_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'translate';
        $this->_controller = 'adminhtml';

        $this->_updateButton('save', 'label', Mage::helper('translate')->__('Save Item'));
        $this->_removeButton('delete');

//        $this->_addButton('saveandcontinue', array(
//            'label' => Mage::helper('adminhtml')->__('Save And Next'),
//            'onclick' => 'saveAndContinueEdit()',
//            'class' => 'save',
//                ), -100);

        $currentId = Mage::getSIngleton('adminhtml/session')->getTranslateId();
        $strings = Mage::getModel("translate/translate")->getCollection();
        foreach ($strings as $string) {
            $id = $string->getId();

//            if ($id != $currentId && $id < $nextId) {
//                $nextId = $id;
//            }
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('translate_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'translate_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'translate_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        return Mage::helper('translate')->__('Edit Item');
    }

}