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
 * @package   RMA
 * @version   1.0.1
 * @revision  135
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Rma_Helper_Field extends Mage_Core_Helper_Abstract
{
	public function getEditableCustomerCollection()
	{
		return Mage::getModel('rma/field')->getCollection()
                    ->addFieldToFilter('is_active', true)
                    ->addFieldToFilter('is_editable_customer', true)
                    ->setOrder('sort_order');
	}

	public function getVisibleCustomerCollection()
	{
		return Mage::getModel('rma/field')->getCollection()
                    ->addFieldToFilter('is_active', true)
                    ->addFieldToFilter('is_visible_customer', true)
                    ->setOrder('sort_order');
	}

	public function getStaffCollection()
	{
		return Mage::getModel('rma/field')->getCollection()
                    ->addFieldToFilter('is_active', true)
                    ->setOrder('sort_order');
	}

    public function getInputParams($field, $staff = true, $object = false)
    {
        return array(
            'label'     => Mage::helper('rma')->__($field->getName()),
            'name'      => $field->getCode(),
            'required'  => $staff? $field->getIsRequiredStaff(): $field->getIsRequiredCustomer(),
            'value'     => $field->getType() == 'checkbox'? 1 : ($object? $object->getData($field->getCode()): ''),
            'checked'   => $object? $object->getData($field->getCode()): false,
            'values'    => $field->getValues(true),
            'image'     => Mage::getDesign()->getSkinUrl('images/grid-cal.gif'),
            'note'		=> $field->getDescription(),
            'format'    => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        );
    }

    public function getInputHtml($field)
    {
    	$params = $this->getInputParams($field, false);
    	unset($params['label']);
        $className = 'Varien_Data_Form_Element_'.ucfirst(strtolower($field->getType()));
        $element = new $className($params);
        $element->setForm(new Varien_Object());
        $element->setId($field->getCode());
        $element->setNoSpan(true);
        $element->addClass($field->getType());
        if ($field->getIsRequiredCustomer()) {
            $element->addClass('required-entry');
        }
        return $element->toHtml();
    }

    public function processPost($post, $object)
    {
		$collection = Mage::helper('rma/field')->getEditableCustomerCollection();
        foreach ($collection as $field) {
            if (isset($post[$field->getCode()])) {
            	$value = $post[$field->getCode()];
                $object->setData($field->getCode(), $value);
            }
            if ($field->getType() == 'checkbox') {
            	if (!isset($post[$field->getCode()])) {
            		$object->setData($field->getCode(), 0);
            	}
            } elseif ($field->getType() == 'date') {
                $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            	Mage::helper('mstcore/date')->formatDateForSave($object, $field->getCode(), $format);
            }
        }
    }

    public function getValue($object, $field)
    {
        $value = $object->getData($field->getCode());
        if (!$value) {
            return false;
        }
        if ($field->getType() == 'checkbox') {
            $value = $value? Mage::helper('rma')->__('Yes'): Mage::helper('rma')->__('No');
        } elseif ($field->getType() == 'date') {
            $value = Mage::helper('core')->formatDate($value, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        } elseif ($field->getType() == 'select') {
            $values = $field->getValues();
            $value = $values[$value];
        }
        return $value;
    }
}