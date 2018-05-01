<?php

class TBT_Rewards_Block_Manage_Metrics_NumberOfMembers_Charts extends Mage_Adminhtml_Block_Template
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
            ->createBlock('rewards/manage_metrics_chart_line', 'rewards.metrics.numberOfMembers.lineChart')
            ->setDataHelper('rewards/metrics_numberOfMembers');
        $this->append($chartBlock, 'rewards.metrics.numberOfMembers.lineChart');

        $allTimeMembersBlock = $this->getLayout()
            ->createBlock('rewards/manage_metrics_chart_textBar', 'rewards.metrics.numberOfMembers.allTimeMembers')
            ->setDataHelper('rewards/metrics_numberOfMembers_allTime');
        $this->insert($allTimeMembersBlock, 'rewards.metrics.numberOfMembers.lineChart',
            true,  'rewards.metrics.numberOfMembers.allTimeMembers');

        $totalMembersBlock = $this->getLayout()
            ->createBlock('rewards/manage_metrics_chart_textBar', 'rewards.metrics.numberOfMembers.totalMembers')
            ->setDataHelper('rewards/metrics_numberOfMembers_total');
        $this->insert($totalMembersBlock, 'rewards.metrics.numberOfMembers.lineChart',
            true,  'rewards.metrics.numberOfMembers.totalMembers');


        return parent::_prepareLayout();
    }
}
