<?php

class TBT_Rewards_Block_Manage_Metrics_Chart_TextBar extends TBT_Rewards_Block_Manage_Metrics_Chart_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rewards/metrics/chart/textBar.phtml');

        return $this;
    }

    public function getChartData()
    {
        $data = $this->getDataHelper()->getAllSeries();
        return $data[0];
    }
}
