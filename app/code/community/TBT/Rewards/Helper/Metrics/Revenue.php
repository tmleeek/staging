<?php

class TBT_Rewards_Helper_Metrics_Revenue extends TBT_Rewards_Helper_Metrics_Chart_Abstract
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

        $this->_collection = Mage::getResourceSingleton('rewards/metrics_revenue_collection')
            ->prepareSummary($period, $storeIds, $from, $to, $transferStatus);

        return $this;
    }

    /**
     * [_prepareSeries description]
     * @param  array  $array [description]
     * @return [type]        [description]
     */
    protected function _prepareSeries($series = array())
    {
        if (is_null($series)) {
            return $this;
        }

        $memberSeries = array();
        foreach ($series as $key => $value) {
            if ($value['is_member']) {
                array_push($memberSeries, $value);
                unset($series[$key]);
            }
        }

        $this->addSeries(array(
            'key'    => 'Members Revenue',
            'values' => $memberSeries
        ));
        $this->addSeries(array(
            'key'    => 'Non-members Revenue',
            'values' => $series
        ));

        $this->_mergeWithEmptyData(
            $this->getParam('period_type'),
            $this->getParam('from'),
            $this->getParam('to'),
            'total_revenue_amount'
        );

        return $this;
    }

    public function getPreSymbol()
    {
        return $this->getCurrentCurrencySymbol();
    }

    public function getYAxisFormat()
    {
        // for now commenting out as NVD3 hasn't updated yet to latest d3 which can directly specify currency formatter
        // $currencySymbol = $this->getCurrentCurrencySymbol();
        return ',.2r';
    }
}
