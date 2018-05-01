<?php

class TBT_Rewards_Block_Manage_Metrics_Earnings_Grid extends TBT_Rewards_Block_Manage_Metrics_Grid_Abstract
{
    protected $_resourceCollectionName = 'rewards/metrics_earnings_collection';

    public function getResourceCollectionName()
    {
        return 'rewards/metrics_earnings_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'          => Mage::helper('rewards')->__('Period'),
            'index'           => 'period',
            'width'           => '50px',
            'sortable'        => false,
            'period_type'     => $this->getPeriodType(),
            'renderer'        => 'adminhtml/report_sales_grid_column_renderer_date',
            'totals_label'    => Mage::helper('rewards')->__('Total'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('distribution_reason', array(
            'header'           => Mage::helper('rewards')->__('Reason'),
            'header_css_class' => 'a-right',
            'align'            => 'right',
            'index'            => 'distribution_reason',
            'width'            => '100px',
            'renderer'         => 'rewards/manage_grid_renderer_distributionReason',
            'totals_label'     => '',
            'sortable'         => false,
        ));

        $this->addColumn('total_points', array(
            'header'           => Mage::helper('rewards')->__('Total Points'),
            'header_css_class' => 'a-right',
            'index'            => 'total_points',
            'width'            => '100px',
            'total'            => 'sum',
            'type'             => 'number',
            'sortable'         => false,
        ));

        if ($this->getFilterData() && $this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $this->addExportType('*/*/exportEarningsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportEarningsExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
