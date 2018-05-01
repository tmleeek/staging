<?php

class TBT_Rewards_Block_Manage_Metrics_Chart_Pie extends TBT_Rewards_Block_Manage_Metrics_Chart_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rewards/metrics/chart/pie.phtml');

        return $this;
    }
}
