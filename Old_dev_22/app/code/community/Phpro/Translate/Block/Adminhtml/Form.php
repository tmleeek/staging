<?php

class Phpro_Translate_Block_Adminhtml_Form extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        parent::__construct();
    }

    protected function _prepareLayout() {
        $onclick = "submitAndReloadArea($('order_history_block').parentNode, '" . $this->getSubmitUrl() . "')";
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
            'label' => Mage::helper('translate')->__('Submit Search'),
            'class' => 'save',
            'onclick' => $onclick
                ));
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    public function getAvailableLangs() {
        $availableLangsArray = explode(',', Mage::getStoreConfig('translate/general/locales'));
        $availableLangs[0]['value'] = '-1';
        $availableLangs[0]['label'] = 'Default';
        foreach ($availableLangsArray as $lang) {
            $tmpLang['value'] = $lang;
            $tmpLang['label'] = $lang;
            $availableLangs[] = $tmpLang;
        }

        return $availableLangs;
    }

    protected function _prepareForm() {
        $onclick = "";
        $form = new Varien_Data_Form(
                        array('id' => 'phpro_search_form',
                            'action' => $this->getUrl('*/*/search', array('id' => $this->getRequest()->getParam('id'))),
                            'method' => 'post')
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        $fieldset = $form->addFieldset('translate_form', array('legend' => Mage::helper('translate')->__('Search for:')));

        $fieldset->addField('q', 'text', array(
            'label' => Mage::helper('translate')->__('Search string'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'q',
            'after_element_html' => '<p class="note">' . Mage::helper('translate')->__('Enter a string (e.g. "My account") and click "Submit search"') . '</p>'
        ));

        $fieldset->addField('keysearch', 'checkbox', array(
            'label' => Mage::helper('translate')->__('Search original strings'),
            'class' => '',
            'required' => false,
            'name' => 'keysearch',
            'value' => 1
        ));
        
        $fieldset->addField('untranslatedsearch', 'checkbox', array(
            'label' => Mage::helper('translate')->__('Only list untranslated'),
            'class' => '',
            'required' => false,
            'name' => 'untranslatedsearch',
            'value' => 1
        ));
        
        //Locales dropdown and default option
        $localesSourceModel = Mage::getModel('translate/system_config_source_locales');
        $localesOptions['all'] = 'All';
        $localesOptions = array_merge($localesOptions, $localesSourceModel->toArray());
        $fieldset->addField('locale', 'select', array(
            'label' => Mage::helper('translate')->__('Locale'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'locale',
            'options' => $localesOptions
        ));
        
        /* To become in expandable container */

        
        /* ============================== */

        $onclick = 'translateSearch(\'' . $this->getUrl('*/*/search') . '\')';
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
            'label' => Mage::helper('translate')->__('Submit Search'),
            'class' => 'save',
            'onclick' => $onclick,
            'id' => 'form_search_submit'
                ));
        $onclickReset = 'translateSearchReset()';
        $buttonReset = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
            'label' => Mage::helper('translate')->__('Reset'),
            'class' => 'back',
            'onclick' => $onclickReset,
                ));

        $fieldset->addField('submit', 'note', array(
            'label' => '',
            'class' => 'button',
            'required' => false,
            'name' => 'submit',
            'text' => $buttonReset->toHtml() . ' ' . $button->toHtml(),
        ));
        
        
        $fieldsetAdvanced = $fieldset->addFieldset('expand', array('legend' => Mage::helper('translate')->__('Options:'),'collapse' => null));
        
        $fieldsetAdvanced->addField('case', 'checkbox', array(
            'label' => Mage::helper('translate')->__('Case sensitive'),
            'class' => '',
            'required' => false,
            'name' => 'case',
            'value' => 1
        ));
        
        $modulesSourceModel = Mage::getModel('translate/system_config_source_modules');
        $modules = $modulesSourceModel->getModulesForForm();
        $fieldsetAdvanced->addField('modules', 'select', array(
            'label' => Mage::helper('translate')->__('In Modules:'),
            'class' => '',
            'required' => false,
            'name' => 'modules',
            'values' => $modules
        ));

        $interfaces = array();
        $interfaces[] = array(
            "value" => "frontend",
            "label" => Mage::helper('translate')->__("Frontend"));
        $interfaces[] = array(
            "value" => "adminhtml",
            "label" => Mage::helper('translate')->__("Admin HTML"));
        $fieldsetAdvanced->addField('interface', 'select', array(
            'label' => Mage::helper('translate')->__('In Interface:'),
            'class' => '',
            'required' => false,
            'values' => $interfaces,
            'name' => 'interface',
        ));
        
        $resultField = $form->addFieldset('result', array('legend' => Mage::helper('translate')->__('Results:')));
        
        

        return parent::_prepareForm();
    }

}
