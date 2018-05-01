<?php

class TBT_Milestone_Model_Rule_Condition_Referrals extends TBT_Milestone_Model_Rule_Condition
{
    // @deprecated use TBT_Milestone_Model_Rule_Condition_Referrals_Reference::REFERENCE_TYPE_ID
    const POINTS_REFERENCE_TYPE_ID = 604;

    protected $_notification_email = true;
    protected $_notification_frontend = false;
    protected $_notification_backend = true;

    public function getMilestoneName()
    {
        return Mage::helper('tbtmilestone')->__("Number of Referrals Milestone");
    }

    public function getMilestoneDescription()
    {
        if (intval($this->getThreshold() == 1)){
            return Mage::helper('tbtmilestone')->__("milestone for referring %s person", $this->getThreshold());

        }  else {
            return Mage::helper('tbtmilestone')->__("milestone for referring %s people", $this->getThreshold());
        }
    }

    public function isSatisfied($customerId)
    {
        $threshold = intval($this->getThreshold());
        $fromDate = $this->getFromDate();
        $toDate = $this->getToDate();

        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer->getId()){
            return false;
        }

        $referralsBeforeStartDate = Mage::getModel('rewardsref/referral')->getCollection()
                                    ->excludeCustomerData()
                                    ->addFieldToFilter('referral_parent_id', $customerId)
                                    ->addFieldToFilter('created_ts' , array("lt" => $fromDate));


        $referralsAfterStartDate = Mage::getModel('rewardsref/referral')->getCollection()
                                    ->excludeCustomerData()
                                    ->addFieldToFilter('referral_parent_id', $customerId)
                                    ->addFieldToFilter('created_ts', array("gteq" => $fromDate));

        if (!empty($toDate)){
            $referralsAfterStartDate->addFieldToFilter(
                    "created_ts", array("lt" => $toDate)
            );
        }

        $storeId = $customer->getStoreId();
        if ($this->_getHelper('config')->isTriggerOnOrderCreate('referrals', $storeId)){
            $this->_joinWithOrders($referralsBeforeStartDate);
            $this->_joinWithOrders($referralsAfterStartDate);

            /*
             * $collection->getSize() is much more efficient, but it will modify the query in this case
             * and return unexpected results, so we can't use it.
             */
            $countBeforeStartDate = $this->_getCount($referralsBeforeStartDate);
            $countAfterStartDate = $this->_getCount($referralsAfterStartDate);

        } else {
            $countBeforeStartDate = $referralsBeforeStartDate->getSize();
            $countAfterStartDate = $referralsAfterStartDate->getSize();
        }

        $countTotal = $countBeforeStartDate + $countAfterStartDate;

        return ( $countBeforeStartDate < $threshold && $countTotal >= $threshold );
    }

    public function validateSave()
    {
        if (!$this->getThreshold()) {
            throw new Exception("The milestone threshold is a required field.");
        }

        return $this;
    }

    /**
     * Will add order data to the specified Referral Collection
     *
     * @param TBT_RewardsReferral_Model_Mysql4_Referral_Collection $collection
     * @retun TBT_RewardsReferral_Model_Mysql4_Referral_Collection. Same collection with modified Select.
     */
    protected function _joinWithOrders(&$collection)
    {
        $ordersTable = $this->_getOrdersTableName();
        $collection->getSelect()
                    ->join( array('orders' => $ordersTable),
                            'main_table.referral_child_id = orders.customer_id',
                            array("number_of_orders" => 'COUNT(*)')
                    )->group('referral_child_id');

        return $collection;
    }

    /**
     * @return string. Name of the table for the sales/order model.
     */
    protected function _getOrdersTableName()
    {
        if (!isset($this->_ordersTable)){
            $this->_ordersTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
        }

        return $this->_ordersTable;
    }

    /**
     * Places the collection's MySQL query inside a sub-query and counts that.
     * Ideal for counting select statements with aggregate functions
     * because ->getSize() won't work and ->count() is inefficient.
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     * @return int
     */
    protected function _getCount($collection)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $countSql = "SELECT COUNT(*) FROM ({$collection->getSelectSql(true)}) AS collection";
        return (int) $connection->fetchOne($countSql);
    }

    /**
     * @return int. The Transfer Refrence Type ID used to identify this type of rule.
     * @see TBT_Milestone_Model_Rule_Condition::getPointsReferenceTypeId()
     */
    public function getPointsReferenceTypeId()
    {
        return TBT_Milestone_Model_Rule_Condition_Referrals_Reference::REFERENCE_TYPE_ID;
    }
}
