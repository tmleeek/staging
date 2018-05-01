<?php

class Tatva_Customerattributes_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getAttributeHiddenFields()
    {
        if (Mage::registry('customer_attribute_type_hidden_fields')) {
            return Mage::registry('customer_attribute_type_hidden_fields');
        } else {
            return array();
        }
    }

    /**
     * Retrieve attribute disabled types
     *
     * @return array
     */
    public function getAttributeDisabledTypes()
    {
        if (Mage::registry('customer_attribute_type_disabled_types')) {
            return Mage::registry('customer_attribute_type_disabled_types');
        } else {
            return array();
        }
    }
}