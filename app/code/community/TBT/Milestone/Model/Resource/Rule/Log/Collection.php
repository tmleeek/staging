<?php

class TBT_Milestone_Model_Resource_Rule_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('tbtmilestone/rule_log');

        return $this;
    }

    /**
     * Filter this collection by rule Id and customer Id
     * @param int $ruleId
     * @param int $customerId
     * @return TBT_Milestone_Model_Resource_Rule_Log_Collection
     */

    public function filterRuleLogsByCustomer($ruleId, $customerId)
    {
        $this->addFieldToFilter('rule_id', $ruleId);
        $this->addFieldToFilter('customer_id', $customerId);

        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        // We need to make sure the rule details are expanded.
        $this->walk('afterLoad');

        return $this;
    }

    /**
     * Joins the collection with customer name. Used in Milestone History grid for filtering on customer name.
     */
    public function addCustomerNameToSelect()
    {
        $firstname      = Mage::getResourceSingleton('customer/customer')->getAttribute('firstname');
        $lastname       = Mage::getResourceSingleton('customer/customer')->getAttribute('lastname');
        $fullExpression = new Zend_Db_Expr("CONCAT(customer_firstname_table.value,' ',customer_lastname_table.value)");

        $this->getSelect()->joinLeft(
            array('customer_lastname_table' => $lastname->getBackend()->getTable()),
            'customer_lastname_table.entity_id = main_table.customer_id
             AND customer_lastname_table.attribute_id = '.(int) $lastname->getAttributeId(),
            array()
        )
        ->joinLeft(
            array('customer_firstname_table' => $firstname->getBackend()->getTable()),
            'customer_firstname_table.entity_id = main_table.customer_id
             AND customer_firstname_table.attribute_id = '.(int) $firstname->getAttributeId(),
            array()
        )
        ->columns(array("customer_name" => $fullExpression));

        $this->getSelect()->from(null, array("customer_name" => $fullExpression));

        $this->_joinFields["customer_name"] = array('table' => false, 'field' => $fullExpression);

        return $this;
    }
}
