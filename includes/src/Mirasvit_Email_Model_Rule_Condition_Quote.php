<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Trigger Email Suite
 * @version   1.0.1
 * @revision  168
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Email_Model_Rule_Condition_Quote extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes =array(
            'quote_items_summary_qty' => Mage::helper('email')->__('Number Product In Cart'),
            'quote_grand_total'       => Mage::helper('email')->__('Amount in cart'),
            'sku'               => Mage::helper('email')->__('Product'),
        );

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();

            $this->_defaultOperatorInputByType['group'] = array('==', '!=', '{}', '!{}', '()', '!()');
            @$this->_arrayInputTypes[] = 'group';
        }
        return $this->_defaultOperatorInputByType;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType()
    {
        $type = 'string';
        switch ($this->getAttribute()) {
            default:
                $type = 'string';
                break;
        }

        return $type;
    }

    public function getValueElementType()
    {
        $type = 'text';
        switch ($this->getAttribute()) {
            case 'customer_group_id':
                $type = 'multiselect';
                break;

            default:
                $type = 'text';
                break;
        }

        return $type;
    }

    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'sku':
                $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="'.$image.'" alt="" class="v-middle rule-chooser-trigger" title="'.__('Open Chooser').'" /></a>';
        }

        return $html;
    }

    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
                $url = 'adminhtml/promo_widget/chooser'
                    .'/attribute/'.$this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/'.$this->getJsFormObject();
                }
                // echo Mage::helper('adminhtml')->getUrl($url);die();
                break;

        }

        return $url!==false ? Mage::helper('adminhtml')->getUrl($url) : '';
    }

    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'sku':
                return true;
        }

        return false;
    }

    public function _prepareValueOptions()
    {
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        $selectOptions = null;

        if ($this->getAttribute() === 'customer_group_id') {
            $selectOptions = Mage::helper('customer')->getGroups()->toOptionArray();
            array_unshift($selectOptions, array('value' => 0, 'label' => Mage::helper('email')->__('Not registered')));
        }

        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = array();
                foreach ($selectOptions as $o) {
                    if (is_array($o['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$o['value']] = $o['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    public function getValueOption($option=null)
    {
        $this->_prepareValueOptions();
        return $this->getData('value_option'.(!is_null($option) ? '/'.$option : ''));
    }

    public function getValueSelectOptions()
    {
        $this->_prepareValueOptions();

        return $this->getData('value_select_options');
    }

    public function validate(Varien_Object $object)
    {
        $attrCode = $this->getAttribute();

        $value = $object->getData($attrCode);

        return $this->validateAttribute($value);
    }

    public function getJsFormObject()
    {
        return 'rule_run_fieldset';
    }
}
