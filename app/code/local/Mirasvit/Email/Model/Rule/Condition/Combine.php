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


class Mirasvit_Email_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('email/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        if ($this->getRule()->getType()) {
            $type = $this->getRule()->getType();
        } else {
            $type = Mage::app()->getRequest()->getParam('rule_type');
        }

        $attributes = array();

        $itemAttributes = $this->_getCustomerAttributes();
        foreach ($itemAttributes as $code => $label) {
            $attributes['Customer'][] = array(
                'value' => 'email/rule_condition_customer|'.$code,
                'label' => $label
            );
        }

        $itemAttributes = $this->_getQuoteAttributes();
        foreach ($itemAttributes as $code => $label) {
            $attributes['Quote'][] = array(
                'value' => 'email/rule_condition_quote|'.$code,
                'label' => $label
            );
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'value' => 'email/rule_condition_combine',
                'label' => Mage::helper('email')->__('Conditions Combination')
            )
        ));

        foreach ($attributes as $group => $arrAttributes) {
            $conditions = array_merge_recursive($conditions, array(
                array(
                    'label' => $group,
                    'value' => $arrAttributes
                ),
            ));
        }

        return $conditions;
    }

    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }


    protected function _getCustomerAttributes()
    {
        $customerCondition   = Mage::getModel('email/rule_condition_customer');
        $customerAttrributes = $customerCondition->loadAttributeOptions()->getAttributeOption();

        return $customerAttrributes;
    }

    protected function _getQuoteAttributes()
    {
        $quoteCondition   = Mage::getModel('email/rule_condition_quote');
        $quoteAttrributes = $quoteCondition->loadAttributeOptions()->getAttributeOption();

        return $quoteAttrributes;
    }

    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            if ($this->getRule()->getType() == 'stop') {
                return false;
            }

            return true;
        }

        $all    = $this->getAggregator() === 'all';
        $true   = (bool)$this->getValue();

        foreach ($this->getConditions() as $cond) {
            $validated = $cond->validate($object);

            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }

        return $all ? true : false;
    }

    public function getId()
    {
        return $this->getRule()->getType().parent::getId();
    }
}
