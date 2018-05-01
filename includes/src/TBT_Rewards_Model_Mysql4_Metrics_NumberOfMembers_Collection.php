<?php

class TBT_Rewards_Model_Mysql4_Metrics_NumberOfMembers_Collection extends TBT_Rewards_Model_Mysql4_Metrics_Collection_Abstract
{

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
     * @return TBT_Rewards_Model_Mysql4_Metrics_NumberOfMembers_Collection
     */
    protected function _initSelect()
    {
        $select  = $this->getSelect();
        $adapter = $this->getConnection();

        $subSelect = $adapter->select()
            ->from(array('transfers' => $this->getTable('rewards/transfer')))
            ->group('customer_id')
            ;

        $select->reset(Zend_Db_Select::FROM)
            ->from(
                array('main_table' => new Zend_Db_Expr('(' . $subSelect . ')')),
                $this->_getSelectedColumns()
            );

        if (!$this->isTotals()) {
            $select->group(array($this->_periodFormat));
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
            $this->_selectedColumns['period']  = $this->_periodFormat;
        }
        $this->_selectedColumns['members'] = 'COUNT(customer_id)';


        return $this->_selectedColumns;
    }

}
