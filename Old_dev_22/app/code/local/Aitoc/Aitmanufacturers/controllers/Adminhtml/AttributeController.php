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
require_once "Mage/Adminhtml/controllers/Catalog/Product/AttributeController.php";
class Aitoc_Aitmanufacturers_Adminhtml_AttributeController extends Mage_Adminhtml_Catalog_Product_AttributeController
{
    
    public function saveconfigAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            /** @var $session Mage_Admin_Model_Session */
            $session = Mage::getSingleton('adminhtml/session');

            $redirectBack   = $this->getRequest()->getParam('back', false);
            /* @var $model Mage_Catalog_Model_Entity_Attribute */
            $model = Mage::getModel('catalog/resource_eav_attribute');
            /* @var $helper Mage_Catalog_Helper_Product */
            $helper = Mage::helper('catalog/product');

            $id = $this->getRequest()->getParam('attribute_id');

            //validate attribute_code
            if (isset($data['attribute_code'])) {
                $validatorAttrCode = new Zend_Validate_Regex('/^[a-zA-Z0-9_]{1,255}$/');
                if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                    $session->addError(
                        $helper->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.'));
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }


            //validate frontend_input
            if (isset($data['frontend_input'])) {
                if (version_compare(Mage::getVersion(), '1.4.1', '>='))
                {
                    /** @var $validatorInputType Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator */
                    $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                    
                    if ($validatorInputType) {
                	if (!$validatorInputType->isValid($data['frontend_input']))
                	{
	                    foreach ($validatorInputType->getMessages() as $message) {
	                        $session->addError($message);
	                    }
	                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
	                    return;
                	}
                    }
                }
            }

            /*** Aitoc Shop By Start ***/
            if ($this->getRequest()->get('config'))
            {
                if (!Mage::getModel('aitmanufacturers/config')->isValid($this->getRequest()->get('config'), !isset($data['frontend_input']) || ($this->getRequest()->get('frontend_input') == 'select')))
                {
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            /***  Aitoc Shop By End  ***/
            
            if ($id) {
                $model->load($id);

                if (!$model->getId()) {
                    $session->addError(
                        Mage::helper('catalog')->__('This Attribute no longer exists'));
                    $this->_redirect('*/*/');
                    return;
                }

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    $session->addError(
                        Mage::helper('catalog')->__('This attribute cannot be updated.'));
                    $session->setAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
            } else {
                /**
                * @todo add to helper and specify all relations for properties
                */
                if (version_compare(Mage::getVersion(), '1.5.0', '<'))
                {
	                $data['source_model'] = $model->getDefaultAttributeSourceModel();
	                $data['backend_model'] = $model->getDefaultAttributeSourceModel();
                }
                else 
                {
                        $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
	                $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
                }
            }

            if (!isset($data['is_configurable'])) {
                $data['is_configurable'] = 0;
            }
            if (!isset($data['is_filterable'])) {
                $data['is_filterable'] = 0;
            }
            if (!isset($data['is_filterable_in_search'])) {
                $data['is_filterable_in_search'] = 0;
            }

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if(!isset($data['apply_to'])) {
                $data['apply_to'] = array();
            }

            //filter
            $data = $this->_filterPostData($data);

            $model->addData($data);

            if (!$id) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }


            if ($this->getRequest()->getParam('set') && $this->getRequest()->getParam('group')) {
                // For creating product attribute on product page we need specify attribute set and group
                $model->setAttributeSetId($this->getRequest()->getParam('set'));
                $model->setAttributeGroupId($this->getRequest()->getParam('group'));
            }

//            try {
                $model->save();
                
                
                /** Start Aitoc Code */
                if ($this->getRequest()->get('config'))
                {
                    Mage::getModel('aitmanufacturers/config')->saveConfigData($this->getRequest()->get('config'), $data['attribute_code']);
                
                    if(Mage::getSingleton('adminhtml/session')->getData('aitmanufacturers_update_stores')===true){
                        Mage::register('aitmanufacturers_update_get_stores', true);
                        Mage::register('aitmanufacturers_fillout_inprogress',true);
                        Mage::getModel('aitmanufacturers/aitmanufacturers')->getCollection()->save();
                        Mage::getSingleton('adminhtml/session')->setData('aitmanufacturers_update_stores',false);
                    }
                }
                 
                /**  End Aitoc Code  */
                
                $session->addSuccess(
                    Mage::helper('catalog')->__('The product attribute has been saved.'));

                /**
                 * Clear translation cache because attribute labels are stored in translation
                 */
                Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                $session->setAttributeData(false);
                if ($this->getRequest()->getParam('popup')) {
                    $this->_redirect('adminhtml/catalog_product/addAttribute', array(
                        'id'       => $this->getRequest()->getParam('product'),
                        'attribute'=> $model->getId(),
                        '_current' => true
                    ));
                } elseif ($redirectBack) {
                    $this->_redirect('*/*/edit', array('attribute_id' => $model->getId(),'_current'=>true));
                } else {
                    $this->_redirect('*/*/', array());
                }
                return;
                
//            } catch (Exception $e) {
//                $session->addError($e->getMessage());
//                $session->setAttributeData($data);
//                $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
//                return;
//            }
        }
        $this->_redirect('*/*/');
    }
    
    public function indexAction()
    {
        
    }
    
    protected function _filterPostData($data)
    {
        if ($data)
        {
            /** @var $helperCatalog Mage_Catalog_Helper_Data */
            $helperCatalog = Mage::helper('catalog');
            //labels
            foreach ($data['frontend_label'] as & $value) {
                if ($value) {
                    $value = strip_tags($value);
                }
            }
            //options
            if (!empty($data['option']['value'])) {
                foreach ($data['option']['value'] as &$options) {
                    foreach ($options as &$label) {
                        $label = strip_tags($label);
                    }
                }
            }
            //default value
            if (!empty($data['default_value'])) {
                $data['default_value'] = strip_tags($data['default_value']);
            }
            if (!empty($data['default_value_text'])) {
                $data['default_value_text'] = strip_tags($data['default_value_text']);
            }
            if (!empty($data['default_value_textarea'])) {
                $data['default_value_textarea'] = strip_tags($data['default_value_textarea']);
            }
        }
        return $data;
    }
}