<?php

class TBT_Rewards_Helper_Metrics_NumberOfMembers_AllTime extends TBT_Rewards_Helper_Metrics_Chart_Abstract
{
    /**
     * Initializes collection of data.
     *
     * @return TBT_Rewards_Helper_Metrics_NumberOfMembers_AllTime
     */
    protected function _initSeries()
    {
        $results = Mage::getResourceSingleton('rewards/metrics_numberOfMembers_collection')
            ->getLoyaltyMembersTotal();

        $this->_prepareSeries($results);

        return $this;
    }

    protected function _prepareSeries($series = array())
    {
        if (is_null($series)) {
            return $this;
        }

        $this->addSeries(array(
            'title'   => 'All Time Loyalty Members',
            'content' => $series,
            // 'note' => ''
        ));

        return $this;
    }
}
