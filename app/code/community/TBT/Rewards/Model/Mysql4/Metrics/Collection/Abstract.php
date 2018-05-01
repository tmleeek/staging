<?php

class TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     * @var string
     **/
    protected $_periodFormat;

    /**
     * Columns for select
     *
     * @var array
     */
    protected $_selectedColumns    = array();

    /**
     * Is Chart flag
     *
     * @var bool
     **/
    protected $_isChart = false;

    /**
     * Transfer status
     *
     * @var string
     */
    protected $_transferStatus = null;

    /**
     * From date
     *
     * @var string
     */
    protected $_from               = null;

    /**
     * To date
     *
     * @var string
     */
    protected $_to                 = null;

    /**
     * Period
     *
     * @var string
     */
    protected $_period             = null;

    /**
     * Store ids
     *
     * @var int|array
     */
    protected $_storesIds          = 0;

    /**
     * Does filters should be applied
     *
     * @var bool
     */
    protected $_applyFilters       = true;

    /**
     * Is totals
     *
     * @var bool
     */
    protected $_isTotals           = false;

    /**
     * Is subtotals
     *
     * @var bool
     */
    protected $_isSubTotals        = false;

    /**
     * Aggregated columns
     *
     * @var array
     */
    protected $_aggregatedColumns  = array();

    /**
     * Set array of columns that should be aggregated
     *
     * @param array $columns
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function setAggregatedColumns(array $columns)
    {
        $this->_aggregatedColumns = $columns;
        return $this;
    }

    /**
     * Retrieve array of columns that should be aggregated
     *
     * @return array
     */
    public function getAggregatedColumns()
    {
        return $this->_aggregatedColumns;
    }

    /**
     * Set date range
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function setDateRange($from = null, $to = null)
    {
        $this->_from = $from;
        $this->_to   = $to;
        return $this;
    }

    /**
     * Set period
     *
     * @param string $period
     * @return Mage_Sales_Model_Resource_Report_Collection_Abstract
     */
    public function setPeriod($period)
    {
        $this->_period = $period;
        return $this;
    }

    /**
     * Set status filter
     *
     * @param string $orderStatus
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    public function addTransferStatusFilter($transferStatus)
    {
        $this->_transferStatus = $transferStatus;
        return $this;
    }

    protected function _applyTransferStatusFilter()
    {
        if (is_null($this->_transferStatus)) {
            return $this;
        }

        $transferStatus = $this->_transferStatus;
        if (!is_array($transferStatus)) {
            $transferStatus = array($transferStatus);
        }
        $this->getSelect()->where('status IN (?)', $transferStatus);

        return $this;
    }

    /**
     * Transfer status filter is custom for this collection
     *
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    protected function _applyCustomFilter()
    {
        $this->_applyTransferStatusFilter();
        return $this;
    }

    /**
     * Apply stores filter to select object, right now we need to join customer table for this.
     *
     * @param Zend_Db_Select $select
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
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
        // we need to join the customer_entity table
        $this->_joinCustomerTable($select);

        if ($nullCheck) {
            $select->where('store_id IN(?) OR store_id IS NULL', $storeIds);
        } else {
            $select->where('store_id IN(?)', $storeIds);
        }

        return $this;
    }

    /**
     * Joins the 'customer_entity' table.
     *
     * @param  Zend_Db_Select $select The select statement.
     * @param  array          $cols   The columns to select from the table.
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    protected function _joinCustomerTable(Zend_Db_Select $select, $cols = array())
    {
        $select->join(
            array('customer' => $this->getTable('customer/entity')),
            'main_table.customer_id = customer.entity_id',
            $cols
        );

        return $this;
    }

    /**
     * Apply date range filter
     *
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    protected function _applyDateRangeFilter()
    {
        if ($this->_from !== null) {
            $this->getSelect()->where('creation_ts >= ?', $this->_from);
        }
        if ($this->_to !== null) {
            $this->getSelect()->where('creation_ts <= ?', $this->_to);
        }

        return $this;
    }

    protected function _setPeriodFormat($period, $column = 'creation_ts')
    {
        if ('month' == $period) {
            $this->_periodFormat = $this->getDateFormatSql($column, '%Y-%m');
        } elseif ('year' == $period) {
            $this->_periodFormat = $this->getDateFormatSql($column, '%Y');
        } else {
            $this->_periodFormat = $this->getDateFormatSql($column, '%Y-%m-%d');
        }

        return $this;
    }

    /**
     * Ported over for compatibility with Magento pre 1.6.
     *
     * Format date as specified
     *
     * Supported format Specifier
     *
     * %H   Hour (00..23)
     * %i   Minutes, numeric (00..59)
     * %s   Seconds (00..59)
     * %d   Day of the month, numeric (00..31)
     * %m   Month, numeric (00..12)
     * %Y   Year, numeric, four digits
     *
     * @param  string $date  quoted date value or non quoted SQL statement(field)
     * @param string $format
     * @return Zend_Db_Expr
     */
    public function getDateFormatSql($date, $format)
    {
        if (Mage::helper('rewards/version')->isBaseMageVersionAtLeast('1.6.0.0')) {
            $adapter = $this->getConnection();
            return $adapter->getDateFormatSql($date, $format);
        }

        $expr = sprintf("DATE_FORMAT(%s, '%s')", $date, $format);

        return new Zend_Db_Expr($expr);
    }

    /**
     * Returns valid IFNULL expression
     * Ported over for compatibility with Magento pre 1.6.
     *
     * @param Zend_Db_Expr|Zend_Db_Select|string $expression
     * @param string $value OPTIONAL. Applies when $expression is NULL
     * @return Zend_Db_Expr
     */
    public function getIfNullSql($expression, $value = 0)
    {
        if (Mage::helper('rewards/version')->isBaseMageVersionAtLeast('1.6.0.0')) {
            $adapter = $this->getConnection();
            return $adapter->getIfNullSql($expression, $value);
        }

        if ($expression instanceof Zend_Db_Expr || $expression instanceof Zend_Db_Select) {
            $expression = sprintf("IFNULL((%s), %s)", $expression, $value);
        } else {
            $expression = sprintf("IFNULL(%s, %s)", $expression, $value);
        }

        return new Zend_Db_Expr($expression);
    }

    /**
     * This is called by the chart data helper classes and applies the filters selected on the frontend on the
     * collection class.
     *
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    public function prepareSummary($period, $storeIds, $from = null, $to = null, $transferStatus)
    {
        $this->setPeriod($period)
            ->setDateRange($from, $to)
            ->addStoreFilter($storeIds)
            ->addTransferStatusFilter($transferStatus);

        return $this;
    }

    /**
     * Returns the total amount of loyalty program members. A customer is considered a member if he has any transfer
     * in the system.
     *
     * @return int
     */
    public function getLoyaltyMembersTotal()
    {
        $adapter = $this->getConnection();
        $subSelect = $adapter->select()
            ->from(array('transfers' => $this->getTable('rewards/transfer')))
            ->reset(Zend_Db_Select::COLUMNS)
            ->group('customer_id')
            ->columns('customer_id');

        $select = $adapter->select()
            ->from($subSelect)
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(('COUNT(customer_id) as total_members'));

        $result = $adapter->fetchOne($select);

        return $result;
    }


    /**
     * Getter/Setter for isChart
     *
     * @param null|boolean $flag
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    public function isChart($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isChart;
        }
        $this->_isChart = $flag;

        return $this;
    }

    /**
     * Set store ids
     *
     * @param mixed $storeIds (null, int|string, array, array may contain null)
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    public function addStoreFilter($storeIds)
    {
        $this->_storesIds = $storeIds;
        return $this;
    }

    /**
     * Apply stores filter
     *
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    protected function _applyStoresFilter()
    {
        return $this->_applyStoresFilterToSelect($this->getSelect());
    }

    /**
     * Set apply filters flag
     *
     * @param boolean $flag
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    public function setApplyFilters($flag)
    {
        $this->_applyFilters = $flag;
        return $this;
    }

    /**
     * Getter/Setter for isTotals
     *
     * @param null|boolean $flag
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    public function isTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isTotals;
        }
        $this->_isTotals = $flag;

        return $this;
    }

    /**
     * Getter/Setter for isSubTotals
     *
     * @param null|boolean $flag
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    public function isSubTotals($flag = null)
    {
        if (is_null($flag)) {
            return $this->_isSubTotals;
        }
        $this->_isSubTotals = $flag;

        return $this;
    }

    /**
    * Just overwriting parent. Not used on transfers collections.
    *
    * @param TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
    */
    public function addOrderStatusFilter($orderStatus)
    {
        return $this;
    }

    /**
     * Load data
     * Redeclare parent load method just for adding method _beforeLoad
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_initSelect();
        if ($this->_applyFilters) {
            $this->_applyDateRangeFilter();
            $this->_applyStoresFilter();
            $this->_applyCustomFilter();
        }

        return parent::load($printQuery, $logQuery);
    }
}
