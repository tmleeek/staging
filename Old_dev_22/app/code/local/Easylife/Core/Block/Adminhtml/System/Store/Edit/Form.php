<?php
class Easylife_Core_Block_Adminhtml_System_Store_Edit_Form extends Mage_Adminhtml_Block_System_Store_Edit_Form{
    protected function _prepareForm(){
        parent::_prepareForm();
        if (Mage::registry('store_type') == 'website'){
            $websiteModel = Mage::registry('store_data');
            $fieldset = $this->getForm()->getElement('website_fieldset');
            $fieldset->addField('language_code', 'text', array(
                    'name'      => 'website[language_code]',
                    'label'     => Mage::helper('core')->__('Language Code'),
                    'required'  => true,//or false
                    'value'        => $websiteModel->getData('language_code')
                ));
            $fieldset->addField('store_fbd_id', 'text', array(
                    'name'      => 'website[store_fbd_id]',
                    'label'     => Mage::helper('core')->__('FBD Store ID'),
                    'required'  => false, //or true, 
                    'value'        => $websiteModel->getData('store_fbd_id')
                ));
        }
        return $this;
    }
}
?>