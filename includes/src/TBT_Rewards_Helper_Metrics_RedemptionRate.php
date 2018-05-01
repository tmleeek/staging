<?php

class TBT_Rewards_Helper_Metrics_RedemptionRate extends TBT_Rewards_Helper_Metrics_Chart_Abstract
{

    /**
     * Initializes series of data.
     *
     * @return TBT_Rewards_Helper_Metrics_RedemptionRate
     */
    protected function _initSeries()
    {
        $this->_initCollection();

        $results = $this->_collection
            ->load()
            ->getData();

        $this->_prepareSeries($results);

        return $this;
    }

    protected function _initCollection()
    {
        if (!is_null($this->_collection)) {
            return $this;
        }

        $period         = $this->getParam('period_type');
        $from           = $this->getParam('from');
        $to             = $this->getParam('to');
        $transferStatus = $this->getParam('transfer_statuses');
        $storeIds       = $this->_getStoreIds();

        $this->_collection = Mage::getResourceSingleton('rewards/metrics_redemptionRate_collection')
            ->prepareSummary($period, $storeIds, $from, $to, $transferStatus);

        return $this;
    }

    /**
     * [_prepareSeries description]
     * @param  array  $series [description]
     * @return [type]         [description]
     */
    protected function _prepareSeries($series = array())
    {
        if (is_null($series)) {
            return $this;
        }

        $this->addSeries(array(
            'key'    => 'Members Redemption Rate',
            'values' => $series
        ));

        $this->_mergeWithEmptyData(
            $this->getParam('period_type'),
            $this->getParam('from'),
            $this->getParam('to'),
            'members_percentage'
        );

        return $this;
    }

    public function getPostSymbol()
    {
        return '%';
    }

    public function getYAxisFormat()
    {
        return '.2g';
    }
}
