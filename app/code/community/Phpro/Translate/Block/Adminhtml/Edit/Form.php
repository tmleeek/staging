<?php

class Phpro_Translate_Block_Adminhtml_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $request = Mage::app()->getRequest();
        $store = $this->getStore($request);
        $formValues = $this->_getFormValues($request, $store);

        $form = new Varien_Data_Form(
                        array(
                            'id' => 'edit_form',
                            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                            'method' => 'post',
                        )
        );
        $this->setForm($form);
        $fieldset = $form->addFieldset('translate_form', array('legend' => Mage::helper('translate')->__('Item information')));


        $fieldset->addField('original_translation_label', 'label', array(
            'label' => Mage::helper('translate')->__('Original'),
            'class' => '',
            'name' => 'module_original',
        ));

        $fieldset->addField('string', (isset($formValues['string']) && strlen($formValues['string']) > 45 ? "textarea" : "text"), array(
            'label' => Mage::helper('translate')->__('String'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'string',
        ));

        $localesSourceModel = Mage::getModel('translate/system_config_source_locales');
        $localesOptions = $localesSourceModel->toArray();
        $fieldset->addField('locale', 'select', array(
            'label' => Mage::helper('translate')->__('Locale'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'locale',
            'options' => $localesOptions
        ));
        
        $fieldsetAdvanced = $fieldset->addFieldset('expand', array('legend' => Mage::helper('translate')->__('Extra Information:'),'collapse' => null));

        $fieldsetAdvanced->addField('module', 'label', array(
            'label' => Mage::helper('translate')->__('Module'),
            'class' => '',
            'name' => 'module',
        ));

        $fieldsetAdvanced->addField('interface', 'label', array(
            'label' => Mage::helper('translate')->__('Interface'),
            'class' => '',
            'name' => 'interface',
        ));

        $fieldsetAdvanced->addField('store_name', 'label', array(
            'label' => Mage::helper('translate')->__('Store'),
            'class' => '',
            'name' => 'store_name',
        ));

        $fieldset->addField('storeid', 'hidden', array(
            'class' => '',
            'name' => 'storeid',
        ));

        $fieldset->addField('original_translation', 'hidden', array(
            'class' => '',
            'name' => 'original_translation'
        ));
        
        $fieldset->addField('namespace', 'hidden', array(
            'class' => '',
            'name' => 'namespace'
        ));
        
        $form->setValues($formValues);

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    private function _getFormValues($request, $store) {
        if (Mage::getSingleton('adminhtml/session')->getTranslateData()) {
            $values = Mage::getSingleton('adminhtml/session')->getTranslateData();
            Mage::getSingleton('adminhtml/session')->setTranslateData(null);
        } elseif (Mage::registry('translate_data')) {
            //fill form with elements of id or requested data
            if ($request->getParam('id')) {
                $values = $this->_getFormValuesFromRegistry();
            } else {
                $values = $this->_getFormValuesFromRequest($request, $store);
            }
            $values['store_name'] = $this->getStoreName($store);
            $values['storeid'] = $store->getId();
        }

        if ($values != null) {
            $values['storeview_specific'] = 1;
        }
        return $values;
    }

    private function _getFormValuesFromRegistry() {
        $values = Mage::registry('translate_data')->getData();
        $explode = explode('::', $values['string']);
        $values['string'] = (isset($explode[1])) ? $explode[1] : $values['string'];
        
        $values['original_translation'] = $values['string'];
        $values['original_translation_label'] = $values['string'];
        $values['namespace'] = Mage::registry('translate_data')->getModule();
        return $values;
    }

    private function _getFormValuesFromRequest($request, $store) {
        $values = array();
        $values['module'] = $request->getParam('modules');
        $translation = explode('::', base64_decode($request->getParam('translation')));
        $values['string'] = (isset($translation[1])) ? $translation[1] : base64_decode($request->getParam('translation'));
        $values['original_translation'] = base64_decode($request->getParam('original'));
        $splitOfOriginalTranslation = explode("::", $values['original_translation']);
        $values['namespace'] = (isset($splitOfOriginalTranslation[1]) ? $splitOfOriginalTranslation[0] : '');
        $values['original_translation_label'] = (isset($splitOfOriginalTranslation[1]) ? $splitOfOriginalTranslation[1] : $splitOfOriginalTranslation[0]);
        $values['interface'] = $request->getParam('interface');
        $values['locale'] = $request->getParam('locale');
        return $values;
    }

    private function getStore($request) {
        $store = null;
        if ($request->getParam('id')) {
            $data = Mage::registry('translate_data')->getData();
            $store = Mage::app()->getStore($data["store_id"]);
        } else {
            $store = Mage::app()->getStore($request->getParam('store'));
        }
        return $store;
    }

    private function getStoreName($store) {
        $storeName = "Main Website";
        if ($store->getId() != 0) {
            $storeName = $store->getName();
        }
        return $storeName;
    }

    public function getHeaderText() {
        return Mage::helper('translate')->__("Edit Item '%s'", 'test');
    }

}