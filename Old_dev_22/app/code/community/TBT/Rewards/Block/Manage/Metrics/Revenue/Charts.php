<?php

class TBT_Rewards_Block_Manage_Metrics_Revenue_Charts extends Mage_Adminhtml_Block_Template
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
            ->createBlock('rewards/manage_metrics_chart_line', 'rewards.metrics.revenue.lineChart')
            ->setDataHelper('rewards/metrics_revenue');
        $this->append($chartBlock, 'rewards.metrics.revenue.lineChart');

        $nonMembersRevenueBlock = $this->getLayout()
            ->createBlock('rewards/manage_metrics_chart_textBar', 'rewards.metrics.revenue.nonMembers')
            ->setDataHelper('rewards/metrics_revenue_nonMembers');
        $this->insert($nonMembersRevenueBlock, 'rewards.metrics.revenue.lineChart',
            true,  'rewards.metrics.revenue.nonMembers');

        $membersRevenueBlock = $this->getLayout()
            ->createBlock('rewards/manage_metrics_chart_textBar', 'rewards.metrics.revenue.members')
            ->setDataHelper('rewards/metrics_revenue_members');
        $this->insert($membersRevenueBlock, 'rewards.metrics.revenue.lineChart',
            true,  'rewards.metrics.revenue.members');


        return parent::_prepareLayout();
    }
}
