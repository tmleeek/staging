<?php

class TBT_Rewards_Model_Mysql4_Customer extends Mage_Customer_Model_Entity_Customer
{
    /**
     * This is to resolve a Magento bug where "is_active" is not recognized as an attribute.
     *
     * @see Mage_Customer_Model_Resource_Customer::_getDefaultAttributes()
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        $defaultAttributes = parent::_getDefaultAttributes();
        $extraAttributes = array('is_active');

        return array_merge($defaultAttributes, $extraAttributes);
    }
}
