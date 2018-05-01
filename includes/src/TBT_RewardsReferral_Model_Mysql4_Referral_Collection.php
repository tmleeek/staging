<?php

class TBT_RewardsReferral_Model_Mysql4_Referral_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_includeCustomerData = true;

    public function _construct()
    {
        $this->_init('rewardsref/referral');
    }

    protected function _beforeLoad()
    {
        if ($this->_includeCustomerData) {
            $this->getSelect()->join(
                    array('cust' => $this->getTable('customer/entity')), 'referral_parent_id = cust.entity_id'
            );
        }

        return parent::_beforeLoad();
    }

    public function excludeCustomerData()
    {
        $this->_includeCustomerData = false;
        return $this;
    }

    public function includeCustomerData()
    {
        $this->_includeCustomerData = true;
        return $this;
    }

    public function addEmailFilter($email, $websiteId = null)
    {
        $this->getSelect()->where('referral_email = ?', $email);

        if ($websiteId) {
            $this->getSelect()->where('`cust`.`website_id` = ?', (int) $websiteId);
        }

        return $this;
    }

    public function addFlagFilter($status)
    {
        $this->getSelect()->where('referral_status = ?', $status);
        return $this;
    }

    public function addClientFilter($id)
    {
        $this->getSelect()->where('referral_parent_id = ?', $id);
        return $this;
    }

    public function addParentNameToSelect()
    {
        $firstname      = Mage::getResourceSingleton('customer/customer')->getAttribute('firstname');
        $lastname       = Mage::getResourceSingleton('customer/customer')->getAttribute('lastname');
        $fullExpression = new Zend_Db_Expr("CONCAT(customer_firstname_table.value,' ',customer_lastname_table.value)");

        $this->getSelect()->joinLeft(
            array('customer_lastname_table' => $lastname->getBackend()->getTable()),
            'customer_lastname_table.entity_id = main_table.referral_parent_id
             AND customer_lastname_table.attribute_id = '.(int) $lastname->getAttributeId() . '
             ',
            array()
        )
        ->joinLeft(
            array('customer_firstname_table' =>$firstname->getBackend()->getTable()),
            'customer_firstname_table.entity_id = main_table.referral_parent_id
             AND customer_firstname_table.attribute_id = '.(int) $firstname->getAttributeId() . '
             ',
            array()
        )
        ->columns(array("parent_name" => $fullExpression));

        $this->getSelect ()->from ( null, array ("parent_name" => $fullExpression ) );
        $this->_joinFields ["parent_name"] = array ('table' => false, 'field' => $fullExpression );

        return $this;
    }

}
