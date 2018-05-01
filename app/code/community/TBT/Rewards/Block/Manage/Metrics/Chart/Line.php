<?php

class TBT_Rewards_Block_Manage_Metrics_Chart_Line extends TBT_Rewards_Block_Manage_Metrics_Chart_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rewards/metrics/chart/line.phtml');

        return $this;
    }
}
