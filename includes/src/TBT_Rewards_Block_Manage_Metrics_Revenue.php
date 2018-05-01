<?php

class TBT_Rewards_Block_Manage_Metrics_Revenue extends TBT_Rewards_Block_Manage_Metrics_Abstract
{
    public function __construct()
    {
        $this->_controller = 'manage_metrics_revenue';
        $this->_headerText = Mage::helper('rewards')->__('Revenue');

        return parent::__construct();
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/revenue', array('_current' => true));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $chartBlock = $this->getLayout()
            ->createBlock('rewards/manage_metrics_revenue_charts', 'rewards.metrics.charts');

        $this->setChild('rewards.metrics.charts', $chartBlock);

        return $this;
    }
}
