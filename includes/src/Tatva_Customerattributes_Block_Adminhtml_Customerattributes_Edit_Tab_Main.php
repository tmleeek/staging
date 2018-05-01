<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product attribute add/edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Tatva_Customerattributes_Block_Adminhtml_Customerattributes_Edit_Tab_Main extends Tatva_Customerattributes_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding product form elements for editing attribute
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        //echo "<pre>";print_r($attributeObject->getData());

        /* @var $form Varien_Data_Form */
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            /*array(
                'value' => 'price',
                'label' => Mage::helper('customerattributes')->__('Price')
            ),*/
            /*array(
                'value' => 'media_image',
                'label' => Mage::helper('customerattributes')->__('Media Image')
            )*/
        );
        if ($attributeObject->getFrontendInput() == 'gallery') {
            $additionalTypes[] = array(
                'value' => 'gallery',
                'label' => Mage::helper('customerattributes')->__('Gallery')
            );
        }

        $response = new Varien_Object();
        $response->setTypes(array());
        //Mage::dispatchEvent('adminhtml_product_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
/*        Mage::register('customer_attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('customer_attribute_type_disabled_types', $_disabledTypes);*/

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();


        $scopes = array(
                        array(
                            'value'=>'0',
                            'label'=>'Store View',
                        ),
                        array(
                            'value'=>'1',
                            'label'=>'global',
                        ),
                        array(
                            'value'=>'2',
                            'label'=>'website',
                        ),
        );


        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('customerattributes')->__('Scope'),
            'title' => Mage::helper('customerattributes')->__('Scope'),
            'note'  => Mage::helper('customerattributes')->__('Declare attribute value saving scope'),
            'values'=> $scopes,
			'disabled' => true,            
        ), 'attribute_code');

        $fieldset->addField('apply_to', 'select', array(
            'name'        => 'apply_to[]',
            'label'       => Mage::helper('customerattributes')->__('Apply To'),
            'values'     => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('customerattributes')->__('All Customers')
              ),
          ),
            'disabled' => true,
        ), 'frontend_class');

        $fieldset->addField('used_in_forms', 'multiselect', array(
            'name'  =>  'used_in_forms[]',
            'label' =>  'Use in froms',
            'values'=>  array(
                              array(
                                  'value' => 'adminhtml_customer',
                                  'label' => 'adminhtml_customer'
                                  ),
                              array(
                                  'value' => 'customer_account_create',
                                  'label' => 'customer_account_create'
                                  ),
                              array(
                                  'value' => 'customer_account_edit',
                                  'label' => 'customer_account_edit'
                                  ),
                              array(
                                  'value' => 'checkout_register',
                                  'label' => 'checkout_register'
                                  ),                                
                            ),
                        ));
        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('customerattributes')->__('Frontend Properties')));


        $fieldset->addField('is_wysiwyg_enabled', 'select', array(
            'name' => 'is_wysiwyg_enabled',
            'label' => Mage::helper('customerattributes')->__('Enable WYSIWYG'),
            'title' => Mage::helper('customerattributes')->__('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ));

        $htmlAllowed = $fieldset->addField('is_html_allowed_on_front', 'select', array(
            'name' => 'is_html_allowed_on_front',
            'label' => Mage::helper('customerattributes')->__('Allow HTML Tags on Frontend'),
            'title' => Mage::helper('customerattributes')->__('Allow HTML Tags on Frontend'),
            'values' => $yesnoSource,
        ));
        if (!$attributeObject->getId() || $attributeObject->getIsWysiwygEnabled()) {
            $attributeObject->setIsHtmlAllowedOnFront(1);
        }


        //$form->getElement('apply_to')->setSize(5);



        // define field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap("is_wysiwyg_enabled", 'wysiwyg_enabled')
            ->addFieldMap("is_html_allowed_on_front", 'html_allowed_on_front')
            ->addFieldMap("frontend_input", 'frontend_input_type')
            ->addFieldDependence('wysiwyg_enabled', 'frontend_input_type', 'textarea')
            ->addFieldDependence('html_allowed_on_front', 'wysiwyg_enabled', '0')
        );

/*        Mage::dispatchEvent('adminhtml_catalog_product_attribute_edit_prepare_form', array(
            'form'      => $form,
            'attribute' => $attributeObject
        ));*/

        return $this;

    }

    /**
     * Retrieve additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'apply'         => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_apply'),
        );
    }
}
