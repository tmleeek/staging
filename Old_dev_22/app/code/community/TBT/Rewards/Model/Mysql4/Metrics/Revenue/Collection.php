<?php

class TBT_Rewards_Model_Mysql4_Metrics_Revenue_Collection extends TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
{
    /**
     * Initialize custom resource model
     */
    public function __construct()
    {
        parent::_construct();

        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('rewards/metrics')->init('sales/order');
        $this->setConnection($this->getResource()->getReadConnection());

        return $this;
    }

    /**
     * Add selected data
     *
     * @return TBT_Rewards_Model_Mysql4_Metrics_Revenue_Collection
     */
    public function _initSelect()
    {
        $select             = $this->getSelect();
        $adapter            = $this->getConnection();
        $orderReferenceType = TBT_Rewards_Model_Transfer_Reference::REFERENCE_ORDER;

        $subSelect = $adapter->select()
            ->from(array('main_table' => $this->getResource()->getMainTable()), array('*'))
            ->joinLeft(
                array('reference_table' => $this->getTable('rewards/transfer_reference')),
                "reference_table.reference_type = {$orderReferenceType}"
                . " AND main_table.entity_id = reference_table.reference_id",
                array()
            )
            ->columns(array(new Zend_Db_Expr("IF (reference_table.rewards_transfer_reference_id IS NOT NULL, 1, 0) as is_member")));

        $select->reset(Zend_Db_Select::FROM)
            ->reset(Zend_Db_Select::COLUMNS)
            ->from(array('main_table' => $subSelect), $this->_getSelectedColumns());

        if (!$this->isTotals()) {
            $select->group(array($this->_periodFormat, 'is_member'));
        }

        return $this;
    }

    /**
     * [_getSelectedColumns description]
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        if ($this->_selectedColumns) {
            return $this->_selectedColumns;
        }

        $select  = $this->getSelect();
        $adapter = $this->getConnection();

        $this->_setPeriodFormat($this->_period, 'created_at');

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        } else {
            $this->_selectedColumns['period']  = $this->_periodFormat;
        }

        $this->_selectedColumns['orders_count'] = new Zend_Db_Expr('COUNT(entity_id)');
        $this->_selectedColumns['is_member'] = 'is_member';
        $this->_selectedColumns['total_revenue_amount'] = new Zend_Db_Expr(
            sprintf('SUM((%s - %s - %s - (%s - %s - %s)) * %s)',
                $this->getIfNullSql('base_total_invoiced', 0),
                $this->getIfNullSql('base_tax_invoiced', 0),
                $this->getIfNullSql('base_shipping_invoiced', 0),
                $this->getIfNullSql('base_total_refunded', 0),
                $this->getIfNullSql('base_tax_refunded', 0),
                $this->getIfNullSql('base_shipping_refunded', 0),
                $this->getIfNullSql('base_to_global_rate', 0)
            )
        );

        return $this->_selectedColumns;
    }


    /**
     * Apply stores filter to select object, right now we need to join customer table for this.
     *
     * @param Zend_Db_Select $select
     * @return TBT_Rewards_Model_Mysql4_Metrics_Revenue_Collection
     */
    protected function _applyStoresFilterToSelect(Zend_Db_Select $select)
    {
        $nullCheck = false;
        $storeIds  = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

        if ($nullCheck) {
            $select->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } else {
            $select->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Apply date range filter
     *
     * @return TBT_Rewards_Model_Mysql4_Metrics_Revenue_Collection
     */
    protected function _applyDateRangeFilter()
    {
        if ($this->_from !== null) {
            $this->getSelect()->where('created_at >= ?', $this->_from);
        }
        if ($this->_to !== null) {
            $this->getSelect()->where('created_at <= ?', $this->_to);
        }

        return $this;
    }
}
