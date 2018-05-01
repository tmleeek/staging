<?php

class TBT_Rewards_Helper_Metrics_NumberOfMembers extends TBT_Rewards_Helper_Metrics_Chart_Abstract
{

    /**
     * Initializes series of data.
     *
     * @return TBT_Rewards_Helper_Metrics_NumberOfMembers
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

        $this->_collection = Mage::getResourceSingleton('rewards/metrics_numberOfMembers_collection')
            ->prepareSummary($period, $storeIds, $from, $to, $transferStatus);

        return $this;
    }

    protected function _prepareSeries($series = array())
    {
        if (is_null($series)) {
            return $this;
        }

        $this->addSeries(array(
            'key'    => 'Number of New Members',
            'values' => $series
        ));

        $this->_mergeWithEmptyData(
            $this->getParam('period_type'),
            $this->getParam('from'),
            $this->getParam('to'),
            'members'
        );

        return $this;
    }

    /**
     * Setting formatting of chart's Y axis values.
     *
     * @return string
     */
    public function getYAxisFormat()
    {
        return ',.2d';
    }
}
