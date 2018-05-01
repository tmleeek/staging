<?php

class TBT_Rewards_Model_Mysql4_Metrics_Earnings_Collection extends TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
{

    /**
     * Total Amount of Points Earned
     *
     * @var int
     **/
    protected $_totalEarnedPoints;

    /**
     * Initialize custom resource model
     */
    public function __construct()
    {
        parent::_construct();

        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('rewards/metrics')->init('rewards/transfer');
        $this->setConnection($this->getResource()->getReadConnection());

        return $this;
    }

    /**
     * Add selected data.
     *
     * @return TBT_Rewards_Model_Mysql4_Metrics_Earnings_Collection
     */
    protected function _initSelect()
    {
        $select  = $this->getSelect();
        $adapter = $this->getConnection();

        $subSelect = $adapter->select()
            ->from(array('transfer_table' => $this->getTable('rewards/transfer')),
                array('customer_id', 'quantity', 'creation_ts', 'status'))
            ->joinLeft(
                array('reference_table' => $this->getTable('rewards/transfer_reference')),
                'transfer_table.rewards_transfer_id = reference_table.rewards_transfer_id'
                . ' AND transfer_table.source_reference_id = reference_table.rewards_transfer_reference_id',
                array()
            )
            ->where('quantity > 0')
            ->columns(array('CONCAT_WS("_", IFNULL(reference_type, 0), reason_id) AS distribution_reason'));

        $select->reset(Zend_Db_Select::FROM)
            ->reset(Zend_Db_Select::COLUMNS)
            ->from(array('main_table' => $subSelect), $this->_getSelectedColumns());

        if (!$this->isTotals()) {
            $select->group(array($this->_periodFormat, 'distribution_reason'));
        } elseif ($this->isChart()) {
            $select->group(array('distribution_reason'));
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

        $this->_setPeriodFormat($this->_period);

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        } else {
            $this->_selectedColumns['period'] = $this->_periodFormat;
        }

        $this->_selectedColumns['distribution_reason'] = 'distribution_reason';
        $this->_selectedColumns['customer_id']         = 'customer_id';
        $this->_selectedColumns['total_points']        = 'SUM(quantity)';

        if ($this->isChart()) {
            $totalEarnedPoints = $this->_getTotalEarnedPoints();
            $this->_selectedColumns['points_percentage'] = sprintf('(SUM(quantity) / %d) * 100', $totalEarnedPoints);
        }

        return $this->_selectedColumns;
    }

    public function prepareSummary($period, $storeIds, $from = null, $to = null, $transferStatus)
    {
        parent::prepareSummary($period, $storeIds, $from, $to, $transferStatus);
        $this->isChart(true)
            ->isTotals(true);

        return $this;
    }

    /**
     * Retrieves the total amount of points earned in the selected period.
     *
     * @return int
     */
    public function _getTotalEarnedPoints()
    {
        if (!is_null($this->_totalEarnedPoints)) {
            return $this->_totalEarnedPoints;
        }

        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(array('transfers' => $this->getTable('rewards/transfer')))
            ->reset(Zend_Db_Select::COLUMNS)
            ->where('quantity > 0')
            ->columns('SUM(quantity)');

        // apply date range filter if needed
        if ($this->_from !== null) {
            $select->where('creation_ts >= ?', $this->_from);
        }
        if ($this->_to !== null) {
            $select->where('creation_ts <= ?', $this->_to);
        }

        $this->_totalEarnedPoints = $adapter->fetchOne($select);

        return $this->_totalEarnedPoints;
    }

}
