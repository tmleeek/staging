<?php

class TBT_Rewards_Model_Mysql4_Customer_Indexer_Points extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Class constructor
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     */
    public function _construct()
    {
        $this->_init ( 'rewards/customer_indexer_points', 'customer_id' );
        return $this;
    }

    /**
     * Reindex all data
     *
     * @name reindexAll
     * @see Mage_Index_Model_Mysql4_Abstract::reindexAll()
     * @return TBT_Rewards_Model_Mysql4_Customer_Indexer_Points
     */
    public function reindexAll()
    {
        $conn = $this->_getWriteAdapter();
        $conn->beginTransaction();
        try {
            // empty index table first
            $this->_createTable ();

            // now generate and insert values for customers who have an existing balance
            $sql = "
                INSERT INTO `{$this->getIdxTable ()}` (customer_id, customer_points_pending_event, customer_points_pending_time, customer_points_pending_approval, customer_points_active, customer_points_usable)
                    SELECT `points_table`.`customer_id`,
                           SUM(`points_table`.`customer_points_pending_event`) AS `customer_points_pending_event`,
                           SUM(`points_table`.`customer_points_pending_time`) AS `customer_points_pending_time`,
                           SUM(`points_table`.`customer_points_pending_approval`) AS `customer_points_pending_approval`,
                           SUM(`points_table`.`customer_points_active`) AS `customer_points_active`,
                           SUM(`points_table`.`customer_points_usable`) AS `customer_points_usable`
                    FROM (
                        -- pending event points
                        SELECT `main_table`.`customer_id`,
                            SUM(main_table.quantity) AS `customer_points_pending_event`,
                            0 AS `customer_points_pending_time`,
                            0 AS `customer_points_pending_approval`,
                            0 AS `customer_points_active`,
                            0 AS `customer_points_usable`
                        FROM `{$this->getTable('rewards/transfer')}` AS `main_table`
                        WHERE (main_table.status IN (4))
                        GROUP BY `main_table`.`customer_id`, `main_table`.`currency_id`

                        UNION ALL

                        -- pending time points
                        SELECT `main_table`.`customer_id`,
                            0 AS `customer_points_pending_event`,
                            SUM( main_table.quantity ) AS `customer_points_pending_time`,
                            0 AS `customer_points_pending_approval`,
                            0 AS `customer_points_active`,
                            0 AS `customer_points_usable`
                        FROM `{$this->getTable('rewards/transfer')}` AS `main_table`
                        WHERE (main_table.status IN (6))
                        GROUP BY `main_table`.`customer_id`, `main_table`.`currency_id`

                        UNION ALL

                        -- pending approval points
                        SELECT `main_table`.`customer_id`,
                            0 AS `customer_points_pending_event`,
                            0 AS `customer_points_pending_time`,
                            SUM(main_table.quantity) AS `customer_points_pending_approval`,
                            0 AS `customer_points_active`,
                            0 AS `customer_points_usable`
                        FROM `{$this->getTable('rewards/transfer')}` AS `main_table`
                        WHERE (main_table.status IN (3))
                        GROUP BY `main_table`.`customer_id`, `main_table`.`currency_id`

                        UNION ALL

                        -- active points
                        SELECT `main_table`.`customer_id`,
                            0 AS `customer_points_pending_event`,
                            0 AS `customer_points_pending_time`,
                            0 AS `customer_points_pending_approval`,
                            SUM( main_table.quantity ) AS `customer_points_active`,
                            SUM(main_table.quantity) AS `customer_points_usable`
                        FROM `{$this->getTable('rewards/transfer')}` AS `main_table`
                        WHERE (main_table.status IN (5))
                        GROUP BY `main_table`.`customer_id`, `main_table`.`currency_id`

                        UNION ALL

                        -- negative pending points
                        SELECT `main_table`.`customer_id`,
                            0 AS `customer_points_pending_event`,
                            0 AS `customer_points_pending_time`,
                            0 AS `customer_points_pending_approval`,
                            0 AS `customer_points_active`,
                            SUM(main_table.quantity) AS `customer_points_usable`
                        FROM `{$this->getTable('rewards/transfer')}` AS `main_table`
                        WHERE (quantity < 0) AND
                              (status IN (4))
                        GROUP BY `main_table`.`customer_id`, `main_table`.`currency_id`
                    ) AS `points_table` GROUP BY `points_table`.`customer_id`;
            ";

            $results = $conn->query($sql);


            // any customer who doesn't have a balance should be added in with 0 balances here (notice "IGNORE")
            $customerTable = Mage::getSingleton('core/resource')->getTableName('customer_entity');
            $sql = "
                INSERT IGNORE INTO `{$this->getIdxTable ()}` (customer_id, customer_points_pending_event, customer_points_pending_time, customer_points_pending_approval, customer_points_active, customer_points_usable)
                    SELECT customer_table.entity_id AS customer_id,
                        0 AS customer_points_pending_event,
                        0 AS customer_points_pending_time,
                        0 AS customer_points_pending_approval,
                        0 AS customer_points_active,
                        0 AS customer_points_usable
                    FROM {$customerTable} as `customer_table`;
            ";
            $results = $conn->query($sql);
            $conn->commit();
        } catch (Exception $ex) {
            $conn->rollBack();
            Mage::helper('rewards/customer_points_index')->invalidate();
            Mage::helper('rewards')->logException($ex);
        }

        return $this;
    }

    /**
     * Re-index Customer's points balance
     * @param  TBT_Rewards_Model_Customer $customer
     * @return $this
     */
    protected function reindexUpdateCustomer($customer)
    {
        $simple_points_balance = $this->_calcPointsBalance($customer);
        $conn = $this->_getWriteAdapter();

        $conn->beginTransaction();
        try {
            $conn->insertOnDuplicate($this->getIdxTable(), $simple_points_balance );
            $conn->commit();
        } catch (Exception $ex) {
            $conn->rollBack();
            Mage::helper('rewards')->logException($ex);
        }


        return $this;
    }

    /**
     * Reindex one customer balance
     *
     * @name reindexUpdate
     * @return TBT_Rewards_Model_Mysql4_Customer_Indexer_Points
     */
    public function reindexUpdate($customer)
    {
        if ($customer != null) {
            return $this->reindexUpdateCustomer($customer);
        }

        $simple_points_balance = $this->_getCustomerBalance ( $customer->getId() );
        $this->_getWriteAdapter ()->insertOnDuplicate ( $this->getIdxTable (), $simple_points_balance );

        return $this;
    }

    /**
     * Reindex one customer balance
     *
     * @name reindexUpdate
     * @return TBT_Rewards_Model_Mysql4_Customer_Indexer_Points
     */
    public function reindexDelete($customerId)
    {
        $adapter = $this->_getWriteAdapter ();
        $where = $adapter->quoteInto ( "{$this->getIdFieldName()} = ?", $customerId );
        $select = $adapter->delete ( $this->getIdxTable (), $where );

        return $this;
    }

    /**
     * Returns the index table name
     *
     * @see Mage_Index_Model_Mysql4_Abstract::getIdxTable()
     * @param mixed $table
     * @return mixed
     */
    public function getIdxTable($table = null)
    {
        return $this->getTable ( 'rewards/customer_indexer_points' );
    }

    /**
     * Returns one customer balance
     *
     * @deprecated Technically this is deprecated and code should't reach here, but leaving this for now.
     * @access protected
     * @param int $customer_id if null, retrieve all the customer balances
     * @return array
     */
    protected function _getCustomerBalance($customer_id = null)
    {
        $customerModel = Mage::getModel ( 'rewards/customer' );
        $customers = $customerModel->getCollection ();

        // If a customer id was specified, filter out list by that customer id.
        if(!empty($customer_id) ) {
            $customers->addFieldToFilter('entity_id', $customer_id);
        }

        $customerIdxData = array ();
        foreach ( $customers as $customer ) {
            $customer = Mage::getModel ( 'rewards/customer' )->load ( $customer->getId () );

            // If the customer id is not there it means that the customer is not complete and should not have a points balance
            if(!$customer->getId()) {
                continue;
            }

            $customerIdxData[] = $this->_calcPointsBalance($customer);
        }

        return $customerIdxData;
    }

    /**
     * Calculates the points balance for a customer
     * @param  TBT_Rewards_Model_Customer $customer
     * @return array Customer Points Balance
     */
    protected function _calcPointsBalance($customer)
    {
        $cp_real_bal = $customer->getRealUsablePoints ();
        $points = $customer->getRealBalanceForPointsStatus ( '*active*' );
        $on_hold_points = $customer->getRealBalanceForPointsStatus ( TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_APPROVAL );
        $pending_points = $customer->getRealBalanceForPointsStatus ( TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_EVENT );
        $pending_time_points = $customer->getRealBalanceForPointsStatus( TBT_Rewards_Model_Transfer_Status::STATUS_PENDING_TIME );

        // TODO: when we start supporting multi currencies, array_sum should be changed!
        $customerIdxData = array (
            'customer_id' => $customer->getId (),
            'customer_points_usable' => array_sum ( $cp_real_bal ),
            'customer_points_pending_event' =>  array_sum ( $pending_points ),
            'customer_points_pending_time' => array_sum ( $pending_time_points ),
            'customer_points_pending_approval' => array_sum ( $on_hold_points ),
            'customer_points_active' => array_sum ( $points )
        );

        return $customerIdxData;
    }

    /**
     * Creates the table for indexing.
     * Instead of deleting/truncating the table if it already exists, dropping and creating again
     * the table as it's more performant (http://dev.mysql.com/doc/refman/5.6/en/optimizing-innodb-ddl-operations.html)
     *
     * @access private
     * @return TBT_Rewards_Model_Mysql4_Customer_Indexer_Points
     */
    private function _createTable()
    {
        // Delete all records
        $this->_getWriteAdapter ()->delete ( $this->getIdxTable () );
        return $this;
    }
}