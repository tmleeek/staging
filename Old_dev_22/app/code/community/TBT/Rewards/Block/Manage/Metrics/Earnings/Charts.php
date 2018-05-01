<?php

class TBT_Rewards_Block_Manage_Metrics_Earnings_Charts extends Mage_Adminhtml_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rewards/metrics/charts.phtml');
        $this->setName('rewards.metrics.charts');

        return $this;
    }

    protected function _prepareLayout()
    {
        $chartBlock = $this->getLayout()
            ->createBlock('rewards/manage_metrics_chart_pie', 'rewards.metrics.earnings.pieChart')
            ->setDataHelper('rewards/metrics_earnings');
        $this->append($chartBlock, 'rewards.metrics.earnings.pieChart');

        return parent::_prepareLayout();
    }
}
